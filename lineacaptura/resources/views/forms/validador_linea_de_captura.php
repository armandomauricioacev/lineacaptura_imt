    public function consultarLineaDePago(Request $request) {
    $lineaDePago = lineaDePago::where([
    ['solicitud', $request->solicitud],
    ['lineaCaptura', $request->lineaDeCaptura]
    ])->get();
    if (utils::enviroment()) {
    $consultar = "http://172.31.10.195:8001/SEPLineaDeCaptura/api/estatusLineaCaptura/LineaDeCaptura";
    } else {
    $consultar = "http://172.31.10.194:8001/SEPLineaDeCaptura/api/estatusLineaCaptura/LineaDeCaptura";
    }
    $json = array(
    "IdDependencia" => substr($request->solicitud, 0, 3),
    "IdSolicitud" => $request->solicitud,
    "LineaCaptura" => $request->lineaDeCaptura,
    );
    try {
    $response = Http::post($consultar, $json);
    if ($response->successful()) {
    if ($response->json()) {
    $response = $response->json();
    } else {
    $response = json_decode(utf8_encode($response->body()), 1);
    }
    if (count($lineaDePago) == 0) {
    $lineaDePago = new lineaDePago();
    } else {
    $lineaDePago = $lineaDePago[0];
    $lineaDePago->estatusPago = $response['Estatus'];
    $lineaDePago->descripcionEstatus = $response['DescripcionEstatus'];
    $lineaDePago->mensajeError = json_encode($response['MensajeError']);
    $lineaDePago->noOperacion = $response['DatosPagos'][0]['NoOperacion'];
    //$lineaDePago->fechaRecepcionPago = $response['DatosPagos'][0]['FechaRecepcionPago'];
    $fechaRecepcionPago = Carbon::parse($response['DatosPagos'][0]['FechaRecepcionPago']);
    if ($fechaRecepcionPago->greaterThan(Carbon::createFromDate(1970, 1, 1))) {
    $fechaRecepcionPago = $fechaRecepcionPago->format('Y-m-d H:i:s');
    } else {
    $fechaRecepcionPago = null;
    }
    $lineaDePago->fechaRecepcionPago = $fechaRecepcionPago;
    $lineaDePago->importePagado = $response['DatosPagos'][0]['ImportePagado'];
    $lineaDePago->jsonDatosPago = json_encode($response);
    if ($lineaDePago->estatusPago == "PAGD" && $lineaDePago->importePagado > 0) {
    $lineaDePago->consultado = 1;
    } else {
    $lineaDePago->descripcionEstatus = 'Línea de captura NO pagada';
    $lineaDePago->estatusPago = "NPAG";
    }
    $lineaDePago->save();
    $lineaDePago->peticion = json_decode($lineaDePago->jsonPeticionCompleta, 1);
    $tramites = array();
    foreach ($lineaDePago->peticion['Tramites']["Tramite"] as $tramite) {
    $t = tramite::where([
    ["variante", $tramite['Variante']],
    ["servicio", $tramite['Homoclave']]
    ])->get();
    if (count($t) > 0) {
    $tramites[] = $t[0];
    }
    }
    $lineaDePago->tramites = $tramites;
    }
    }
    } catch (\Throwable $th) {
    //dd($th);
    }
    $data = array(
    'lp' => $lineaDePago,
    'response' => $response
    );
    return view('layouts.lp.show', $data);
    }


    public function generarLineaDePago(Request $request) {
    $lp = new lineaDePago();
    $dg = dg::find($request->id_dg);
    $dg->contador = ++$dg->contador;
    $dg->save();
    $fechaActual = Carbon::now()->timezone('America/Costa_Rica'); // Regresa la zona horaria correcta
    $solicitud = $dg->direccion . $dg->clave . $fechaActual->format('y') . str_pad($dg->contador, 10, "0", STR_PAD_LEFT);
    // $fechaActual = Carbon::now()->timezone('America/Mexico_City'); // Actualmente esta regresando UTC-5 y no UTC-6
    $vigencia = 0;

    $total = 0;
    $numeroTramite = 0;
    $tramites = array(); // arreglo para guardar el id de los trámites
    $camposAdicionales = array(); // arreglo para guardar los valores ingresados por los campos adicionales

    $json = array(
    "DatosGenerales" => [
    "Solicitud" => $solicitud,
    "CveDependencia" => $dg->direccion,
    "UnidadAdministrativa" => $dg->clave,
    "TipoPersona" => $request->tipo_persona,
    "DatosLineaCaptura" => [
    "FechaSolicitud" => $fechaActual->format('d/m/Y H:i'),
    "FechaVigencia" => '',
    "Importe" => ''
    ]
    ],
    "Tramites" => [
    "Tramite" => []
    ]
    );
    $total = 0;
    $numeroTramite = 0;

    if ($request->idTramites) {
    foreach ($request->idTramites as $tramite) {
    $item = tramite::find($tramite);
    // revisamos los campos opcionales que tenemos en base al trÃ¡mite agregado para registrar las respuestas de los usuarios
    foreach ($item->campos as $campoAdicional) {
    $camposAdicionales[] = new tbl_lineasdepago_campos([
    'campo' => $campoAdicional->nombre,
    'valor' => ($request['campoAdicional_' . $campoAdicional->id] ? $request['campoAdicional_' . $campoAdicional->id] : ''),
    'tbl_lineasdepago_id' => '',
    'campos_tramites_id' => $campoAdicional->id,
    ]);
    }
    // agregamos la información del trámite por cada arreglo asi guardamos valores
    $tramites[] = new tbl_lineasdepago_tramites([
    'descripcion' => $item->descripcion,
    'cantidad' => $request->cantidadTramites,
    'cuota' => round($item->cuota * $request->cantidadTramites),
    'tramite_id' => $item->id,
    'tbl_lineasdepago_id' => '',
    ]);

    if ($vigencia < $item->vigencia_lc) {
        $vigencia = $item->vigencia_lc;
        }
        $numeroTramite++;
        $totalTramite = round($item->cuota * $request->cantidadTramites);
        if ($totalTramite == 0) {
        $totalTramite = $request->tramiteVariable[$tramite] * $request->cantidadTramites;
        }
        $tramite = [
        "NumeroTramite" => $numeroTramite,
        "Homoclave" => $item->servicio,
        "Variante" => (string)$item->variante,
        "NumeroConceptos" => 1,
        "TotalTramite" => $totalTramite,
        "Conceptos" => [
        "Concepto" => [
        [
        "NumeroSecuencia" => $numeroTramite,
        "ClaveConcepto" => $item->clave_contable,
        "Agrupador" => [
        "IdAgrupador" => $item->agrupador,
        "TipoAgrupador" => $item->tipo_argumento,
        ],
        "DatosIcep" => [
        "ClavePeriodicidad" => "N",
        "ClavePeriodo" => "099",
        "FechaCausacion" => $fechaActual->format('d/m/Y')
        ],
        "TotalContribuciones" => $totalTramite,
        "TotalConcepto" => $totalTramite,
        "DP" => [
        "TransaccionP" => [
        [
        "ClaveTransaccion" => "4011",
        "ValorTransaccion" => $totalTramite
        ],
        [
        "ClaveTransaccion" => "4243",
        "ValorTransaccion" => $totalTramite
        ],
        [
        "ClaveTransaccion" => "4423",
        "ValorTransaccion" => $totalTramite
        ]
        ]
        ]
        ]
        ]
        ]
        ];
        $total = $total + $totalTramite;
        array_push($json["Tramites"]["Tramite"], $tramite);
        }
        }

        if ($request->idTramitesCompuestos) {
        $numero_secuencia = 0;
        foreach ($request->idTramitesCompuestos as $tramiteCompuesto) {
        $tramite = array();
        $tramite_compuesto = tramitesCompuestos::find($tramiteCompuesto);
        foreach ($tramite_compuesto->tramites as $item) {

        foreach ($item->campos as $campoAdicional) {
        $camposAdicionales[] = new tbl_lineasdepago_campos([
        'campo' => $campoAdicional->nombre,
        'valor' => ($request['campoAdicional_' . $campoAdicional->id] ? $request['campoAdicional_' . $campoAdicional->id] : ''),
        'tbl_lineasdepago_id' => '',
        'campos_tramites_id' => $campoAdicional->id,
        ]);
        }

        // agregamos la información del trámite por cada arreglo asi guardamos valores
        $tramites[] = new tbl_lineasdepago_tramites([
        'descripcion' => $item->descripcion,
        'cantidad' => $request->cantidadTramites,
        'cuota' => round($item->cuota * $request->cantidadTramites),
        'tramite_id' => $item->id,
        'tbl_lineasdepago_id' => '',
        ]);

        if ($vigencia < $item->vigencia_lc) {
            $vigencia = $item->vigencia_lc;
            }
            $numero_secuencia++;
            $totalTramite = round($item->cuota * $request->cantidadTramites);
            if ($totalTramite == 0 && $item->tipo_argumento == 'P') {
            $totalTramite = round($request->tramiteVariable[$tramiteCompuesto] * $request->cantidadTramites);
            }
            if ($totalTramite == 0 && $item->tipo_argumento == 'S') {
            $totalTramite = round($request->tramiteVariable[$tramiteCompuesto] * 0.16 * $request->cantidadTramites);
            }
            $total = $total + $totalTramite;
            if ($item->tipo_argumento == 'P') {
            $numeroTramite++;
            $tramite[$tramiteCompuesto] = [
            "NumeroTramite" => $numeroTramite,
            "Homoclave" => $item->servicio,
            "Variante" => (string)$item->variante,
            "NumeroConceptos" => count($tramite_compuesto->tramites),
            "TotalTramite" => $totalTramite,
            "Conceptos" => [
            "Concepto" => [
            [
            "NumeroSecuencia" => $numero_secuencia,
            "ClaveConcepto" => $item->clave_contable,
            "Agrupador" => [
            "IdAgrupador" => $item->agrupador,
            "TipoAgrupador" => $item->tipo_argumento,
            ],
            "DatosIcep" => [
            "ClavePeriodicidad" => "N",
            "ClavePeriodo" => "099",
            "FechaCausacion" => $fechaActual->format('d/m/Y')
            ],
            "TotalContribuciones" => $totalTramite,
            "TotalConcepto" => $totalTramite,
            "DP" => [
            "TransaccionP" => [
            [
            "ClaveTransaccion" => "4011",
            "ValorTransaccion" => $totalTramite
            ],
            [
            "ClaveTransaccion" => "4243",
            "ValorTransaccion" => $totalTramite
            ],
            [
            "ClaveTransaccion" => "4423",
            "ValorTransaccion" => $totalTramite
            ]
            ]
            ]
            ]
            ]
            ]
            ];
            } else if ($item->tipo_argumento == 'S') {
            $tramite[$tramiteCompuesto]["Conceptos"]["Concepto"][] =
            [
            "NumeroSecuencia" => $numero_secuencia,
            "ClaveConcepto" => $item->clave_contable,
            "Agrupador" => [
            "IdAgrupador" => $item->agrupador,
            "TipoAgrupador" => $item->tipo_argumento,
            ],
            "DatosIcep" => [
            "ClavePeriodicidad" => "N",
            "ClavePeriodo" => "099",
            "FechaCausacion" => $fechaActual->format('d/m/Y')
            ],
            "TotalContribuciones" => $totalTramite,
            "TotalConcepto" => $totalTramite,
            "DP" => [
            "TransaccionP" => [
            [
            "ClaveTransaccion" => "4011",
            "ValorTransaccion" => $totalTramite
            ],
            [
            "ClaveTransaccion" => "4243",
            "ValorTransaccion" => $totalTramite
            ],
            [
            "ClaveTransaccion" => "4423",
            "ValorTransaccion" => $totalTramite
            ]

            ]
            ]
            ];
            $tramite[$tramiteCompuesto]['TotalTramite'] += $totalTramite;
            }
            }
            array_push($json["Tramites"]["Tramite"], $tramite[$tramiteCompuesto]);
            }
            }

            $json['DatosGenerales']['DatosLineaCaptura']['Importe'] = round($total);
            if($request->formaDePago){
            $fechaVigencia = $fechaActual->addDays(0); // ponemos la línea de captura generada para pago en linea sin vigencia para que la paguen ese mismo día
            }else{
            $fechaVigencia = $fechaActual->addDays($vigencia);
            }

            $json['DatosGenerales']['DatosLineaCaptura']['FechaVigencia'] = $fechaVigencia->format('d/m/Y');

            if ($request->tipo_persona == "F") {
            $json['DatosGenerales']['CURP'] = strtoupper($request->curp);
            $json['DatosGenerales']['RFC'] = strtoupper($request->rfc_f);
            $json['DatosGenerales']['Nombre'] = strtoupper($request->nombres);
            $json['DatosGenerales']['ApellidoPaterno'] = strtoupper($request->primer_apellido);
            if ($request->segundo_apellido != null || $request->segundo_apellido != '') {
            $json['DatosGenerales']['ApellidoMaterno'] = strtoupper($request->segundo_apellido);
            }
            }

            if ($request->tipo_persona == "M") {
            $json['DatosGenerales']['RFC'] = strtoupper($request->rfc_m);
            $json['DatosGenerales']['RazonSocial'] = strtoupper($request->razon_social);
            }
            try {
            if (utils::enviroment()) {
            $response = Http::retry(2, 100)->post('http://172.31.10.195:8001/SEPLineaDeCaptura/api/peticionLineaCaptura/LineaDeCaptura', $json);
            } else {
            $response = Http::retry(1, 100)->post('http://172.31.10.194:8001/SEPLineaDeCaptura/api/peticionLineaCaptura/LineaDeCaptura', $json);
            }
            if ($response->successful()) {
            if ($response->json()) {
            $data = $response->json();
            } else {
            $data = json_decode(utf8_encode($response->body()), 1);
            }

            if ($data) {
            if ($data["DatosGenerales"]["Resultado"] == 0) {
            $lp->FechaVigencia = Carbon::createFromFormat('d/m/Y', $data['Acuse']['DatosLineaCaptura']['FechaVigencia'])->format('Y/m/d');
            $lp->Importe = $data['Acuse']['DatosLineaCaptura']['Importe'];
            $lp->LineaCaptura = $data['Acuse']['DatosLineaCaptura']['LineaCaptura'];
            $lp->TipoPago = $data['Acuse']['DatosLineaCaptura']['TipoPago'];
            $lp->html = $data['Acuse']['HTML'];
            $lp->idDocumento = $data['DatosGenerales']['IdDocumento'];
            $lp->Resultado = $data['DatosGenerales']['Resultado'];
            $lp->solicitud = $data['DatosGenerales']['Solicitud'];
            $lp->JsonSolicitud = json_encode($data['Solicitud']);
            } else {
            $lp->idDocumento = $data['DatosGenerales']['IdDocumento'];
            $lp->Resultado = $data['DatosGenerales']['Resultado'];
            $lp->solicitud = $data['DatosGenerales']['Solicitud'];
            $lp->JsonSolicitud = json_encode($data['Solicitud']);
            $lp->jsonErrores = json_encode($data['Errores']);
            }
            } else {
            $lp->solicitud = $solicitud;
            $lp->Resultado = "4";
            $lp->jsonErrores = "La respuesta del servidor esta vacía";
            }
            } else {
            $lp->solicitud = $solicitud;
            $lp->Resultado = "3";
            $lp->jsonErrores = "No se pudo completar la transacción";
            }
            } catch (\Exception $e) {
            $lp->solicitud = $solicitud;
            $lp->jsonErrores = $e->getMessage();
            $lp->Resultado = "5";
            }
            $lp->dg_id = $request->id_dg;
            $lp->jsonPeticionCompleta = json_encode($json);
            $lp->formaDePago = $request->formaDePago;
            $lp->mp_email = $request->mp_email;
            $lp->mp_phone = $request->mp_phone;
            if ($lp->save()) {
            foreach ($tramites as $tramite) {
            $tramite->tbl_lineasdepago_id = $lp->id;
            $tramite->save();
            }
            foreach ($camposAdicionales as $campoAdicional) {
            $campoAdicional->tbl_lineasdepago_id = $lp->id;
            $campoAdicional->save();
            }
            }

            //$lp->tramites = $request->idTramites;
            if ($lp->Resultado == 0) {
            if ($request->formaDePago) {

            $lp->hash = hash_hmac('sha256', $lp->solicitud . $lp->LineaCaptura . number_format($lp->Importe, 2, '.', ''), "14a6A8dc3bfd3Dd11a2Sf31cf51971084836C48fed2630237f55a7b76c9B6baf5");
            $lp->save();
            $lp->Importe = number_format($lp->Importe, 2, '.', '');
            $lp->mp_customername = '';
            if ($request->tipo_persona == "F") {
            $lp->mp_customername = $request->nombres . ' ' . $request->primer_apellido;
            if ($request->segundo_apellido != null || $request->segundo_apellido != '') {
            $lp->mp_customername .= ' ' . $request->segundo_apellido;
            }
            } else {
            $lp->mp_customername = $request->RazonSocial;
            }
            } else {
            $lp->pdf = html_entity_decode(base64_decode($lp->html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $lp->download = explode('" download', explode('<a href="', $lp->pdf)[1])[0];
            }
        }

        return back()->with([" response"=> $lp]);
                }


                // Función para validar la CURP
                validaCurp(curp: string) {
                // Expresión regular para validar la estructura de la CURP
                const regex = /^[A-Z][AEIOUX][A-Z][A-Z][0-9]{2}[0-9]{2}[0-9]{2}[HMX][A-Z]{2}[^0-9AEIOU][^0-9AEIOU][^0-9AEIOU][0-9A-J][0-9]$/;
                // Verifica si la CURP cumple con la expresión regular
                if (regex.test(curp)) {
                // Extrae el dígito verificador de la CURP
                const digitoProporcionado = parseInt(curp.charAt(17), 10);
                // Calcula el dígito verificador de los primeros 17 caracteres
                const digitoCalculado = this.digitoVerificador(curp.substring(0, 17));
                // Compara el dígito verificador calculado con el proporcionado
                return digitoCalculado === digitoProporcionado;
                }
                return false;
                }
                // Función para calcular el dígito verificador de la CURP
                digitoVerificador(string: string) {
                const caracteres = '0123456789ABCDEFGHIJKLMN*OPQRSTUVWXYZ';
                let factor = 19;
                let suma = 0;

                // Recorre los caracteres del string
                for (let i = 0; i < string.length; i++) {
                    factor--;
                    const char=string.charAt(i);
                    const pos=caracteres.indexOf(char);
                    suma +=pos * factor;
                    }

                    // Calcula el dígito verificador
                    const digito=10 - suma % 10;
                    return digito===10 ? 0 : digito;
                    }