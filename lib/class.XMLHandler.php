<?php

class XMLHandler {

    private $datos;
    private $datos_json;
    private $mysqlHandler;
    private $imgFilesCorrelation;

    /**
     * Instanciamos un objeto de la clase XMLHandler para mediante sus funciones realizar una
     * carga de datos a partir de ficheros XML que son suministrados a la funcion loadFromFile. Se debe inicializar
     * con un objeto de la clase MySQL para procesar las peticiones a la base de datos.
     * 
     * @param phpDataClass $mysqlHandler es el puntero al objeto que se encarga de manejar las conexiones y peticiones a la base de datos.
     */
    public function __construct($mysqlHandler = null) {
        $this->datos = FALSE;
        $this->datos_json = false;
        $this->mysqlHandler = $mysqlHandler;
        $this->imgFilesCorrelation = array();
    }

    private function insert() {
        $datos = $this->datos;

        // analizamos a ver si el comercial existe, sino lo creamos.
        $comercialNumero = $datos['anbieter']['immobilie']['kontaktperson']['personennummer'];
        $query = "SELECT ID FROM comerciales WHERE Personal_number='$comercialNumero'";
        
        $resource = $this->mysqlHandler->executeQuery($query);
        $datosComercial = mysql_fetch_assoc($resource);
        
        if (isset($datosComercial['ID'])) {
            //existe el comercial.
            $comercialId = $datosComercial['ID'];
        } else {

            //no existe el comercial, procedo a añadirlo.
            $email_central = $datos['anbieter']['immobilie']['kontaktperson']['email_zentrale'];
            $tel_central = $datos['anbieter']['immobilie']['kontaktperson']['tel_zentrale'];
            $tel_ext = $datos['anbieter']['immobilie']['kontaktperson']['tel_durchw'];
            $tel_fax = $datos['anbieter']['immobilie']['kontaktperson']['tel_fax'];
            $styling = $datos['anbieter']['immobilie']['kontaktperson']['anrede'];
            $styling_letter = $datos['anbieter']['immobilie']['kontaktperson']['anrede_brief'];

            $company = $datos['anbieter']['immobilie']['kontaktperson']['firma'];
            $comercialescol = $datos['anbieter']['immobilie']['kontaktperson'][''];
            $codigo_postal = $datos['anbieter']['immobilie']['kontaktperson']['plz'];
            $location = $datos['anbieter']['immobilie']['kontaktperson']['ort'];
            $direccion = $datos['anbieter']['immobilie']['kontaktperson']['strasse'];
            $direccion_numero = $datos['anbieter']['immobilie']['kontaktperson']['hausnummer'];
            $iso_land = $datos['anbieter']['immobilie']['kontaktperson']['land']['@attributes']['iso_land'];
            $url = $datos['anbieter']['immobilie']['kontaktperson']['url'];
            $Personal_number = $datos['anbieter']['immobilie']['kontaktperson']['personennummer'];

            $query = "INSERT INTO comerciales(id,email_central,tel_central,tel_ext,tel_fax,styling,styling_letter,company,comercialescol,codigo_postal,location,direccion,direccion_numero,iso_land,url,Personal_number)
                    VALUES(NULL, '$email_central','$tel_central','$tel_ext','$tel_fax','$styling','$styling_letter','$company','$comercialescol','$codigo_postal','$location','$direccion','$direccion_numero','$iso_land','$url','$Personal_number')";

            $this->mysqlHandler->executeQuery($query);

            $comercialId = $this->mysqlHandler->lastID();
        }



        $fecha_insert = $datos['uebertragung']['@attributes']['timestamp'];

        $uso = $datos['anbieter']['immobilie']['objektkategorie']['nutzungsart']['@attributes']['WOHNEN'] > 0 ? // si es comercial (gewerbe) o vivienda.
                $datos['anbieter']['immobilie']['objektkategorie']['nutzungsart']['@attributes']['WOHNEN'] : // en la tabla esta enum 1=>vivienda
                $datos['anbieter']['immobilie']['objektkategorie']['nutzungsart']['@attributes']['GEWERBE'] + 1;      // y 2=>comercial. Por eso el +1 sobre el 1 de comercial

        $tipo_venta = $datos['anbieter']['immobilie']['objektkategorie']['vermarktungsart']['@attributes']['KAUF'] > 0 ? //si es venta o alquiler.
                $datos['anbieter']['immobilie']['objektkategorie']['vermarktungsart']['@attributes']['KAUF'] : // 1=venta
                $datos['anbieter']['immobilie']['objektkategorie']['vermarktungsart']['@attributes']['MIETE_PACHT'] + 1;      // 2=alquiler

        $tipo_vivienda = isset($datos['anbieter']['immobilie']['objektkategorie']['objektart']['haus']['@attributes']['haustyp']) ?
                $datos['anbieter']['immobilie']['objektkategorie']['objektart']['haus']['@attributes']['haustyp'] :
                $datos['anbieter']['immobilie']['objektkategorie']['objektart'][0]['@attributes']['haustyp'];

        $geo_ubicacion = $datos['anbieter']['immobilie']['geo']['ort'];
        $geo_cordLatitud = $datos['anbieter']['immobilie']['geo']['geokoordinaten']['@attributes']['breitengrad'];
        $geo_cordLongitud = $datos['anbieter']['immobilie']['geo']['geokoordinaten']['@attributes']['laengengrad'];
        $geo_calle = $datos['anbieter']['immobilie']['geo']['strasse'];
        $geo_numero = $datos['anbieter']['immobilie']['geo']['hausnummer'];
        $geo_piso = $datos['anbieter']['immobilie']['geo']['anzahl_etagen'];
        $geo_pais = $datos['anbieter']['immobilie']['geo']['land']['@attributes']['iso_land'];


        $precio_compra = $datos['anbieter']['immobilie']['preise']['kaufpreis'];
        $precio_casa = $datos['anbieter']['immobilie']['preise']['hausgeld'];
        $precio_metro_cuadrado = $datos['anbieter']['immobilie']['preise']['kaufpreis_pro_qm'];
        $precio_tipo_moneda = $datos['anbieter']['immobilie']['preise']['waehrung']['@attributes']['iso_waehrung'];

        $precio_cochera_compraContado = $datos['anbieter']['immobilie']['preise']['stp_carport']['@attributes']['stellplatzkaufpreis'];
        $precio_cochera_compraContado_ext = $datos['anbieter']['immobilie']['preise']['stp_carport']['@attributes']['anzahl'];

        $precio_duplex_compraContado = $datos['anbieter']['immobilie']['preise']['stp_duplex']['@attributes']['stellplatzmiete'];
        $precio_duplex_compraContado_ext = $datos['anbieter']['immobilie']['preise']['stp_duplex']['@attributes']['anzahl'];

        $precio_espacioLibre_compraContado = $datos['anbieter']['immobilie']['preise']['stp_freiplatz']['@attributes']['stellplatzmiete'];
        $precio_espacioLibre_compraContado_ext = $datos['anbieter']['immobilie']['preise']['stp_freiplatz']['@attributes']['anzahl'];

        $precio_garaje_compraContado = $datos['anbieter']['immobilie']['preise']['stp_garage']['@attributes']['stellplatzmiete'];
        $precio_garaje_compraContado_ext = $datos['anbieter']['immobilie']['preise']['stp_garage']['@attributes']['anzahl'];

        $precio_garajePisos_compraContado = $datos['anbieter']['immobilie']['preise']['stp_parkhaus']['@attributes']['stellplatzmiete'];
        $precio_garajePisos_compraContado_ext = $datos['anbieter']['immobilie']['preise']['stp_parkhaus']['@attributes']['anzahl'];

        $precio_aparcamientoSubterraneo_compraContado = $datos['anbieter']['immobilie']['preise']['stp_tiefgarage']['@attributes']['stellplatzmiete'];
        $precio_aparcamientoSubterraneo_compraContado_ext = $datos['anbieter']['immobilie']['preise']['stp_tiefgarage']['@attributes']['anzahl'];

        $precio_otros_compraContado = $datos['anbieter']['immobilie']['preise']['stp_sonstige']['@attributes']['stellplatzmiete'];
        $precio_otros_compraContado_ext = $datos['anbieter']['immobilie']['preise']['stp_sonstige']['@attributes']['anzahl'];

        $subasta = is_array($datos['anbieter']['immobilie']['versteigerung']) ? // que tipo de datos viene aqui?, atributo?, string dentro de...
                "" :
                $datos['anbieter']['immobilie']['versteigerung'];

        $areas_habitable = $datos['anbieter']['immobilie']['flaechen']['wohnflaeche'];
        $areas_util = $datos['anbieter']['immobilie']['flaechen']['nutzflaeche'];
        $areas_terreno = $datos['anbieter']['immobilie']['flaechen']['grundstuecksflaeche'];
        $areas_apartamento = $datos['anbieter']['immobilie']['flaechen']['einliegerwohnung'];
        $areas_numero_sala = $datos['anbieter']['immobilie']['flaechen']['anzahl_zimmer'];
        $areas_numero_habitaciones = $datos['anbieter']['immobilie']['flaechen']['anzahl_schlafzimmer'];
        $areas_numero_wc = $datos['anbieter']['immobilie']['flaechen']['anzahl_badezimmer'];
        $areas_numero_wc2 = $datos['anbieter']['immobilie']['flaechen']['anzahl_sep_wc'];
        $areas_numero_terrazas = $datos['anbieter']['immobilie']['flaechen']['balkon_terrasse_flaeche'];
        $areas_numero_parkings = $datos['anbieter']['immobilie']['flaechen']['anzahl_stellplaetze'];


        //esta tampoco lo tengo muy claro
        $equipo_cocina = isset($datos['anbieter']['immobilie']['ausstattung']['kueche']['@attributes']['EBK']) ? //si no esta definido
                $datos['anbieter']['immobilie']['ausstattung']['kueche']['@attributes']['EBK'] + 1 : //le asigno por defecto NO
                1;                                                                                          //enum(no, si). Si esta definido sera x+1 (1 o 2).
        $equipo_cocina_ebk = $equipo_cocina;

        $equipo_cocina_abierto = isset($datos['anbieter']['immobilie']['ausstattung']['kueche']['@attributes']['OFFEN']) ?
                $datos['anbieter']['immobilie']['ausstattung']['kueche']['@attributes']['OFFEN'] + 1 :
                1;

        //array, string, atributos...
        $equipo_suelo = is_array($datos['anbieter']['immobilie']['ausstattung']['boden']) ?
                "" :
                $datos['anbieter']['immobilie']['ausstattung']['boden'];

        $equipo_chimenea = $datos['anbieter']['immobilie']['ausstattung']['kamin'] + 1;

        $equipo_calefacion_estufa = isset($datos['anbieter']['immobilie']['ausstattung']['heizungsart']['@attributes']['OFEN']) ? //primero evaluamos si esta definido
                $datos['anbieter']['immobilie']['ausstattung']['heizungsart']['@attributes']['OFEN'] + 1 : //y a continuacion si es asi le sumamos
                1;                                                                                                                  //1, porque en la tabla esta
        $equipo_calefacion_suelos = isset($datos['anbieter']['immobilie']['ausstattung']['heizungsart']['@attributes']['ETAGE']) ? //enum('no', 'si'), con lo cual si de   
                $datos['anbieter']['immobilie']['ausstattung']['heizungsart']['@attributes']['ETAGE'] + 1 : //entrada viene 0 se queda a 1, que es no
                1;                                                                                                                  //y si viene 1 sale 2, que es si.
        $equipo_calefacion_central = isset($datos['anbieter']['immobilie']['ausstattung']['heizungsart']['@attributes']['ZENTRAL']) ?
                $datos['anbieter']['immobilie']['ausstattung']['heizungsart']['@attributes']['ZENTRAL'] + 1 :
                1;
        $equipo_calefacion_remota = isset($datos['anbieter']['immobilie']['ausstattung']['heizungsart']['@attributes']['FERN']) ?
                $datos['anbieter']['immobilie']['ausstattung']['heizungsart']['@attributes']['FERN'] + 1 :
                1;
        $equipo_calefacion_piso2 = isset($datos['anbieter']['immobilie']['ausstattung']['heizungsart']['@attributes']['FUSSBODEN']) ?
                $datos['anbieter']['immobilie']['ausstattung']['heizungsart']['@attributes']['FUSSBODEN'] + 1 :
                1;

        $equipo_alumbrado_aceite = isset($datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['OEL']) ? //primero evaluamos si esta definido
                $datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['OEL'] + 1 : //y a continuacion si es asi le sumamos
                1;                                                                                                                  //1, porque en la tabla esta
        $equipo_alumbrado_gas = isset($datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['GAS']) ? //enum('no', 'si'), con lo cual si de  
                $datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['GAS'] + 1 : //entrada viene 0 se queda a 1, que es no
                1;                                                                                                                  //y si viene 1 sale 2, que es si.
        $equipo_alumbrado_electrico = isset($datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['ELEKTRO']) ?
                $datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['ELEKTRO'] + 1 :
                1;
        $equipo_alumbrado_alternativo = isset($datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['ALTERNATIV']) ?
                $datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['ALTERNATIV'] + 1 :
                1;
        $equipo_alumbrado_solar = isset($datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['SOLAR']) ?
                $datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['SOLAR'] + 1 :
                1;
        $equipo_alumbrado_geotermica = isset($datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['ERDWAERME']) ?
                $datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['ERDWAERME'] + 1 :
                1;
        $equipo_alumbrado_aerea = isset($datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['LUFTWP']) ?
                $datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['LUFTWP'] + 1 :
                1;
        $equipo_alumbrado_pellet = isset($datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['PELLET']) ?
                $datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['PELLET'] + 1 :
                1;

        $equipo_ac = isset($datos['anbieter']['immobilie']['ausstattung']['klimatisiert']) ?
                $datos['anbieter']['immobilie']['ausstattung']['klimatisiert'] + 1 :
                1;


        $equipo_aparcamiento_exterior_garaje = isset($datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['GARAGE']) ?
                $datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['GARAGE'] + 1 :
                1;
        $equipo_aparcamiento_exterior_garaje2 = isset($datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['TIEFGARAGE']) ?
                $datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['TIEFGARAGE'] + 1 :
                1;
        $equipo_aparcamiento_exterior_cochera = isset($datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['CARPORT']) ?
                $datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['CARPORT'] + 1 :
                1;
        $equipo_aparcamiento_exterior_aireLibre = isset($datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['FREIPLATZ']) ?
                $datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['FREIPLATZ'] + 1 :
                1;
        $equipo_aparcamiento_exterior_parking = isset($datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['PARKHAUS']) ?
                $datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['PARKHAUS'] + 1 :
                1;
        $equipo_aparcamiento_exterior_duplex = isset($datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['DUPLEX']) ?
                $datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['DUPLEX'] + 1 :
                1;



        if (isset($datos['anbieter']['immobilie']['ausstattung']['fahrstuhl']['@attributes']['PERSONEN'])) {
            $equipo_ascensor = 2;
            $equipo_ascensor_personas = $datos['anbieter']['immobilie']['ausstattung']['fahrstuhl']['@attributes']['PERSONEN'];
            $equipo_ascensor_carga = $datos['anbieter']['immobilie']['ausstattung']['fahrstuhl']['@attributes']['LASTEN'];
        } else {
            $equipo_ascensor = 1;
            $equipo_ascensor_personas = "";
            $equipo_ascensor_carga = "";
        }

        $equipo_adaptado_minusvalidos = isset($datos['anbieter']['immobilie']['ausstattung']['rollstuhlgerecht']) ?
                $datos['anbieter']['immobilie']['ausstattung']['rollstuhlgerecht'] + 1 :
                1;
        $equipo_satelite = isset($datos['anbieter']['immobilie']['ausstattung']['kabel_sat_tv']) ?
                $datos['anbieter']['immobilie']['ausstattung']['kabel_sat_tv'] + 1 :
                1;
        $equipo_sin_barreras = isset($datos['anbieter']['immobilie']['ausstattung']['barrierefrei']) ?
                $datos['anbieter']['immobilie']['ausstattung']['barrierefrei'] + 1 :
                1;
        $equipo_sauna = isset($datos['anbieter']['immobilie']['ausstattung']['sauna']) ?
                $datos['anbieter']['immobilie']['ausstattung']['sauna'] + 1 :
                1;
        $equipo_piscina = isset($datos['anbieter']['immobilie']['ausstattung']['swimmingpool']) ?
                $datos['anbieter']['immobilie']['ausstattung']['swimmingpool'] + 1 :
                1;
        $equipo_jardin_invierno = isset($datos['anbieter']['immobilie']['ausstattung']['wintergarten']) ?
                $datos['anbieter']['immobilie']['ausstattung']['wintergarten'] + 1 :
                1;
        $equipo_inst_deportivas = isset($datos['anbieter']['immobilie']['ausstattung']['sporteinrichtungen']) ?
                $datos['anbieter']['immobilie']['ausstattung']['sporteinrichtungen'] + 1 :
                1;
        $equipo_zona_bienestar = isset($datos['anbieter']['immobilie']['ausstattung']['wellnessbereich']) ?
                $datos['anbieter']['immobilie']['ausstattung']['wellnessbereich'] + 1 :
                1;

        $equipo_sotano = isset($datos['anbieter']['immobilie']['ausstattung']['unterkellert']['@attributes']['keller']) ?
                $datos['anbieter']['immobilie']['ausstattung']['unterkellert']['@attributes']['keller'] :
                "";

        $equipo_espacio_bicis = isset($datos['anbieter']['immobilie']['ausstattung']['fahrradraum']) ?
                $datos['anbieter']['immobilie']['ausstattung']['fahrradraum'] + 1 :
                1;

        if (isset($datos['anbieter']['immobilie']['ausstattung']['dachform']['@attributes'])) {
            $equipo_tipo_tejado = implode(",", array_keys($datos['anbieter']['immobilie']['ausstattung']['dachform']['@attributes']));
        } else {
            $equipo_tipo_tejado = "";
        }

        $equipo_biblioteca = isset($datos['anbieter']['immobilie']['ausstattung']['bibliothek']) ?
                $datos['anbieter']['immobilie']['ausstattung']['bibliothek'] + 1 :
                1;
        $equipo_atico = isset($datos['anbieter']['immobilie']['ausstattung']['dachboden']) ?
                $datos['anbieter']['immobilie']['ausstattung']['dachboden'] + 1 :
                1;
        $equipo_aseo_invitados = isset($datos['anbieter']['immobilie']['ausstattung']['gaestewc']) ?
                $datos['anbieter']['immobilie']['ausstattung']['gaestewc'] + 1 :
                1;
        $equipo_personas_mayores = isset($datos['anbieter']['immobilie']['ausstattung']['seniorengerecht']) ?
                $datos['anbieter']['immobilie']['ausstattung']['seniorengerecht'] + 1 :
                1;


        $estado_construccion = isset($datos['anbieter']['immobilie']['zustand_angaben']['baujahr']) ?
                $datos['anbieter']['immobilie']['zustand_angaben']['baujahr'] :
                "";

        $estado_estado = isset($datos['anbieter']['immobilie']['zustand_angaben']['zustand']['@attributes']['zustand_art']) ?
                $datos['anbieter']['immobilie']['zustand_angaben']['zustand']['@attributes']['zustand_art'] :
                "";
        $estado_energetico_fgeewert = isset($datos['anbieter']['immobilie']['zustand_angaben']['energiepass']['fgeewert']) ?
                $datos['anbieter']['immobilie']['zustand_angaben']['energiepass']['fgeewert'] :
                "";
        $estado_energetico_epart = isset($datos['anbieter']['immobilie']['zustand_angaben']['energiepass']['epart']) ?
                $datos['anbieter']['immobilie']['zustand_angaben']['energiepass']['epart'] :
                "";

//supongo que el siguiente es ok.
        $estado_energetico_valido = is_array($datos['anbieter']['immobilie']['zustand_angaben']['energiepass']['gueltig_bis']) ? //enum (1="si", 2="no")
                2 :
                $datos['anbieter']['immobilie']['zustand_angaben']['energiepass']['gueltig_bis'];

        $estado_energetico_demanda = isset($datos['anbieter']['immobilie']['zustand_angaben']['energiepass']['endenergiebedarf']) ?
                $datos['anbieter']['immobilie']['zustand_angaben']['energiepass']['endenergiebedarf'] :
                "";


        $estado_venta = isset($datos['anbieter']['immobilie']['zustand_angaben']['verkaufstatus']['@attributes']['stand']) ?
                $datos['anbieter']['immobilie']['zustand_angaben']['verkaufstatus']['@attributes']['stand'] :
                "";

        $valoracion = is_array($datos['anbieter']['immobilie']['bewertung']) ?
                "" :
                $datos['anbieter']['immobilie']['bewertung'];


        $infraestructura_distancia_escuela = isset($datos['anbieter']['immobilie']['distanzen'][0]) ?
                $datos['anbieter']['immobilie']['distanzen'][0] :
                0;

        $infraestructura_distancia_instituto = isset($datos['anbieter']['immobilie']['distanzen'][1]) ?
                $datos['anbieter']['immobilie']['distanzen'][1] :
                0;
        $infraestructura_distancia_aereopuerto = isset($datos['anbieter']['immobilie']['distanzen'][2]) ?
                $datos['anbieter']['immobilie']['distanzen'][2] :
                0;
        $infraestructura_distancia_estacionRemota = isset($datos['anbieter']['immobilie']['distanzen'][3]) ?
                $datos['anbieter']['immobilie']['distanzen'][3] :
                0;
        $infraestructura_distancia_autovia = isset($datos['anbieter']['immobilie']['distanzen'][4]) ?
                $datos['anbieter']['immobilie']['distanzen'][4] :
                0;
        $infraestructura_distancia_tren = isset($datos['anbieter']['immobilie']['distanzen'][5]) ?
                $datos['anbieter']['immobilie']['distanzen'][5] :
                0;
        $infraestructura_distancia_bus = isset($datos['anbieter']['immobilie']['distanzen'][6]) ?
                $datos['anbieter']['immobilie']['distanzen'][6] :
                0;
        $infraestructura_distancia_restaurante = isset($datos['anbieter']['immobilie']['distanzen'][7]) ?
                $datos['anbieter']['immobilie']['distanzen'][7] :
                0;
        $infraestructura_distancia_centro = isset($datos['anbieter']['immobilie']['distanzen'][8]) ?
                $datos['anbieter']['immobilie']['distanzen'][8] :
                0;


        $extra_titulo = is_array($datos['anbieter']['immobilie']['freitexte']['objekttitel']) ?
                "" :
                $datos['anbieter']['immobilie']['freitexte']['dreizeiler'];
        $extra_ziler = is_array($datos['anbieter']['immobilie']['freitexte']['dreizeiler']) ?
                "" :
                $datos['anbieter']['immobilie']['freitexte']['dreizeiler'];
        $extra_ubicacion = is_array($datos['anbieter']['immobilie']['freitexte']['lage']) ?
                "" :
                $datos['anbieter']['immobilie']['freitexte']['lage'];
        $extra_restriccion_ausstatt = isset($datos['anbieter']['immobilie']['freitexte']['ausstatt_beschr']) ?
                $datos['anbieter']['immobilie']['freitexte']['ausstatt_beschr'] :
                "";
        $extra_descripcion = isset($datos['anbieter']['immobilie']['freitexte']['objektbeschreibung']) ?
                $datos['anbieter']['immobilie']['freitexte']['objektbeschreibung'] :
                "";
        $extra_otras = isset($datos['anbieter']['immobilie']['freitexte']['sonstige_angaben']) ?
                $datos['anbieter']['immobilie']['freitexte']['sonstige_angaben'] :
                "";
        $inmueble_xml_id = $datos['anbieter']['immobilie']['verwaltung_techn']['openimmo_obid'];


        $query = "INSERT INTO inmueble(
             `id`,
             `fecha_insert`,
            `uso`,
            `tipo_venta`,        
            `tipo_vivienda`,
            `geo_ubicacion`,
            `geo_cordLatitud`,
            `geo_cordLongitud`,
            `geo_calle`,
            `geo_numero`,
            `geo_piso`,
            `geo_pais`,
            `precio_compra`,
            `precio_casa`,
            `precio_metro_cuadrado`,
            `precio_tipo_moneda`,
            `precio_cochera_compraContado`,
            `precio_cochera_compraContado_ext`,
            `precio_duplex_compraContado`,
            `precio_duplex_compraContado_ext`,
            `precio_espacioLibre_compraContado`,
            `precio_espacioLibre_compraContado_ext`,
            `precio_garaje_compraContado`,
            `precio_garaje_compraContado_ext`,
            `precio_garajePisos_compraContado`,
            `precio_garajePisos_compraContado_ext`,
            `precio_aparcamientoSubterraneo_compraContado`,
            `precio_aparcamientoSubterraneo_compraContado_ext`,
            `precio_otros_compraContado`,
            `precio_otros_compraContado_ext`,
            `subasta`,
            `areas_habitable`,
            `areas_util`,
            `areas_numero_sala`,
            `areas_numero_habitaciones`,
            `areas_numero_wc`,
            `areas_numero_wc2`,
            `areas_numero_terrazas`,
            `areas_numero_parkings`,
            `equipo_cocina`,                    
            `equipo_cocina_ebk`,
            `equipo_suelo`,
            `equipo_chimenea`,            
            `equipo_calefacion_estufa`,
            `equipo_calefacion_suelos`,
            `equipo_calefacion_central`,
            `equipo_calefacion_remota`,
            `equipo_calefacion_piso2`,
            `equipo_alumbrado_aceite`,
            `equipo_alumbrado_gas`,
            `equipo_alumbrado_electrico`,
            `equipo_alumbrado_alternativo`,
            `equipo_alumbrado_solar`,
            `equipo_alumbrado_geotermica`,
            `equipo_alumbrado_aerea`,
            `equipo_alumbrado_pellet`,
            
            `equipo_aparcamiento_exterior_garaje`,
            `equipo_aparcamiento_exterior_garaje2`,
            `equipo_aparcamiento_exterior_cochera`,
            `equipo_aparcamiento_exterior_aireLibre`,
            `equipo_aparcamiento_exterior_parking`,
            `equipo_aparcamiento_exterior_duplex`,
            
            `equipo_ac`,
            `equipo_ascensor`,
            `equipo_ascensor_personas`,
            `equipo_ascensor_carga`,
            `equipo_adaptado_minusvalidos`,
            `equipo_satelite`,                    
            `equipo_sin_barreras`,
            `equipo_sauna`,
            `equipo_piscina`,
            `equipo_jardin_invierno`,
            `equipo_inst_deportivas`,
            `equipo_zona_bienestar`,
            `equipo_sotano`,
            `equipo_espacio_bicis`,
            `equipo_tipo_tejado`,
            `equipo_biblioteca`,
            `equipo_atico`,
            `equipo_aseo_invitados`,
            `equipo_personas_mayores`,
            `estado_construccion`,
            `estado_estado`,
            `estado_energetico_fgeewert`,
            `estado_energetico_epart`,
            `estado_energetico_valido`,
            `estado_energetico_demanda`,
            `estado_venta`,
            `valoracion`,            
            `infraestructura_distancia_escuela`,
            `infraestructura_distancia_instituto`,
            `infraestructura_distancia_aereopuerto`,
            `infraestructura_distancia_estacionRemota`,
            `infraestructura_distancia_autovia`,
            `infraestructura_distancia_tren`,
            `infraestructura_distancia_bus`,
            `infraestructura_distancia_restaurante`,
            `infraestructura_distancia_centro`,
            `extra_titulo`,
            `extra_ziler`,
            `extra_ubicacion`,
            `extra_restriccion_ausstatt`,
            `extra_descripcion`,
            `extra_otras`,
            `json_total`,
            `inmueble_xml_id`,
            `comercial_id`,
            `areas_terreno`,
            `areas_apartamento`,
            `equipo_cocina_abierto`)
    VALUES (
            NULL,
            '$fecha_insert',
            '$uso',
            '$tipo_venta',        
            '$tipo_vivienda',
            '$geo_ubicacion',
            '$geo_cordLatitud',
            '$geo_cordLongitud',
            '$geo_calle',
            '$geo_numero',
            '$geo_piso',
            '$geo_pais',
            '$precio_compra',
            '$precio_casa',
            '$precio_metro_cuadrado',
            '$precio_tipo_moneda',
            '$precio_cochera_compraContado',
            '$precio_cochera_compraContado_ext',
            '$precio_duplex_compraContado',
            '$precio_duplex_compraContado_ext',
            '$precio_espacioLibre_compraContado',
            '$precio_espacioLibre_compraContado_ext',
            '$precio_garaje_compraContado',
            '$precio_garaje_compraContado_ext',
            '$precio_garajePisos_compraContado',
            '$precio_garajePisos_compraContado_ext',
            '$precio_aparcamientoSubterraneo_compraContado',
            '$precio_aparcamientoSubterraneo_compraContado_ext',
            '$precio_otros_compraContado',
            '$precio_otros_compraContado_ext',
            '$subasta',
            '$areas_habitable',
            '$areas_util',
            '$areas_numero_sala',
            '$areas_numero_habitaciones',
            '$areas_numero_wc',
            '$areas_numero_wc2',
            '$areas_numero_terrazas',
            '$areas_numero_parkings',
            '$equipo_cocina',                    
            '$equipo_cocina_ebk',
            '$equipo_suelo',
            '$equipo_chimenea',     
            '$equipo_calefacion_estufa',
            '$equipo_calefacion_suelos',
            '$equipo_calefacion_central',
            '$equipo_calefacion_remota',
            '$equipo_calefacion_piso2',
            '$equipo_alumbrado_aceite',
            '$equipo_alumbrado_gas',
            '$equipo_alumbrado_electrico',
            '$equipo_alumbrado_alternativo',
            '$equipo_alumbrado_solar',
            '$equipo_alumbrado_geotermica',
            '$equipo_alumbrado_aerea',
            '$equipo_alumbrado_pellet',
                
            '$equipo_aparcamiento_exterior_garaje',
            '$equipo_aparcamiento_exterior_garaje2',
            '$equipo_aparcamiento_exterior_cochera',
            '$equipo_aparcamiento_exterior_aireLibre',
            '$equipo_aparcamiento_exterior_parking',
            '$equipo_aparcamiento_exterior_duplex',
            
            '$equipo_ac',
            '$equipo_ascensor',
            '$equipo_ascensor_personas',
            '$equipo_ascensor_carga',
            '$equipo_adaptado_minusvalidos',
            '$equipo_satelite',                    
            '$equipo_sin_barreras',
            '$equipo_sauna',
            '$equipo_piscina',
            '$equipo_jardin_invierno',
            '$equipo_inst_deportivas',
            '$equipo_zona_bienestar',
            '$equipo_sotano',
            '$equipo_espacio_bicis',
            '$equipo_tipo_tejado',
            '$equipo_biblioteca',
            '$equipo_atico',
            '$equipo_aseo_invitados',
            '$equipo_personas_mayores',
            '$estado_construccion',
            '$estado_estado',
            '$estado_energetico_fgeewert',
            '$estado_energetico_epart',
            '$estado_energetico_valido',
            '$estado_energetico_demanda',
            '$estado_venta',
            '$valoracion',            
            '$infraestructura_distancia_escuela',
            '$infraestructura_distancia_instituto',
            '$infraestructura_distancia_aereopuerto',
            '$infraestructura_distancia_estacionRemota',
            '$infraestructura_distancia_autovia',
            '$infraestructura_distancia_tren',
            '$infraestructura_distancia_bus',
            '$infraestructura_distancia_restaurante',
            '$infraestructura_distancia_centro',
            '$extra_titulo',
            '$extra_ziler',
            '$extra_ubicacion',
            '$extra_restriccion_ausstatt',
            '$extra_descripcion',
            '$extra_otras',
            '" . $this->datos_json . "',
            '$inmueble_xml_id',
            '$comercialId',
            '$areas_terreno',
            '$areas_apartamento',
            '$equipo_cocina_abierto'
    )";

//            echo $query;
//            exit();

        if ( $this->mysqlHandler->executeQuery($query) ) {

            $inmuebleId = $this->mysqlHandler->lastID();

            $arrayTraduccionTiposImagenes = array("TITELBILD" => 1, "BILD" => 2, "KARTEN_LAGEPLAN" => 3);

            //procedemos a registrar las imagenes.                
            $queryInsertValues = "";

            foreach ($datos['anbieter']['immobilie']['anhaenge']['anhang'] as $fotosArray) {

                $tipo = $arrayTraduccionTiposImagenes[$fotosArray['@attributes']['gruppe']];
                $titulo = $fotosArray['anhangtitel'];
                //garantizamos que sea unico el nombre del fichero donde esta la foto.
                $ficheroPath = $inmuebleId . "_" . substr(sha1($fotosArray['daten']['pfad']), 0, 9) . "." . $fotosArray['format'];

                $queryInsertValues .= "(NULL, '$inmuebleId', '$tipo', '$titulo', '$ficheroPath'), ";

                //este atributo contiene el array relacional entre el nombre antiguo de las imagenes y su nuevo nombre.
                $this->imgFilesCorrelation[$fotosArray['daten']['pfad']] = $ficheroPath;
            }


            $queryInsertValues = trim($queryInsertValues, ", ");


            if ($queryInsertValues != "") {
                $query = "INSERT INTO inmueble_img(id,inmueble_id,tipo,titulo,fichero) VALUES $queryInsertValues";

                $this->mysqlHandler->executeQuery($query);
            }
        }
        else { return 'Error: '.$this->mysqlHandler->lastError(); }
    }

    private function update() {

        $datos = $this->datos;


        // analizamos a ver si el comercial existe, sino lo creamos.
        $comercialNumero = $datos['anbieter']['immobilie']['kontaktperson']['personennummer'];
        $query = "SELECT ID FROM comerciales WHERE Personal_number='$comercialNumero'";
        $datosComercial = $this->mysqlHandler->executeQuery($query);


        $email_central = $datos['anbieter']['immobilie']['kontaktperson']['email_zentrale'];
        $tel_central = $datos['anbieter']['immobilie']['kontaktperson']['tel_zentrale'];
        $tel_ext = $datos['anbieter']['immobilie']['kontaktperson']['tel_durchw'];
        $tel_fax = $datos['anbieter']['immobilie']['kontaktperson']['tel_fax'];
        $styling = $datos['anbieter']['immobilie']['kontaktperson']['anrede'];
        $styling_letter = $datos['anbieter']['immobilie']['kontaktperson']['anrede_brief'];

        $company = $datos['anbieter']['immobilie']['kontaktperson']['firma'];
        $comercialescol = $datos['anbieter']['immobilie']['kontaktperson'][''];
        $codigo_postal = $datos['anbieter']['immobilie']['kontaktperson']['plz'];
        $location = $datos['anbieter']['immobilie']['kontaktperson']['ort'];
        $direccion = $datos['anbieter']['immobilie']['kontaktperson']['strasse'];
        $direccion_numero = $datos['anbieter']['immobilie']['kontaktperson']['hausnummer'];
        $iso_land = $datos['anbieter']['immobilie']['kontaktperson']['land']['@attributes']['iso_land'];
        $url = $datos['anbieter']['immobilie']['kontaktperson']['url'];
        $Personal_number = $datos['anbieter']['immobilie']['kontaktperson']['personennummer'];


        if (isset($datosComercial['ID'])) {
            //existe el comercial. Actualizamos sus datos
            $comercialId = $datosComercial['ID'];

            $query = "UPDATE comerciales SET
                            email_central='$email_central',
                            tel_central='$tel_central',
                            tel_ext='$tel_ext',
                            tel_fax='$tel_fax',
                            styling='$styling',
                            styling_letter='$styling_letter',
                            company='$company',
                            comercialescol='$comercialescol',
                            codigo_postal='$codigo_postal',
                            location='$location',
                            direccion='$direccion',
                            direccion_numero='$direccion_numero',
                            iso_land='$iso_land',
                            url='$url',
                            Personal_number='$Personal_number'                
                        WHERE Personal_number='$comercialNumero'";

            $this->mysqlHandler->executeQuery($query);
        } 
        else {
            //no existe el comercial, procedo a añadirlo.

            $query = "INSERT INTO comerciales(id,email_central,tel_central,tel_ext,tel_fax,styling,styling_letter,company,comercialescol,codigo_postal,location,direccion,direccion_numero,iso_land,url,Personal_number)
                    VALUES(NULL, '$email_central','$tel_central','$tel_ext','$tel_fax','$styling','$styling_letter','$company','$comercialescol','$codigo_postal','$location','$direccion','$direccion_numero','$iso_land','$url','$Personal_number')";

            $this->mysqlHandler->executeQuery($query);

            $comercialId = $this->mysqlHandler->lastID();
        }


        $fecha_insert = $datos['uebertragung']['@attributes']['timestamp'];

        $uso = $datos['anbieter']['immobilie']['objektkategorie']['nutzungsart']['@attributes']['WOHNEN'] > 0 ? // si es comercial (gewerbe) o vivienda.
                $datos['anbieter']['immobilie']['objektkategorie']['nutzungsart']['@attributes']['WOHNEN'] : // en la tabla esta enum 1=>vivienda
                $datos['anbieter']['immobilie']['objektkategorie']['nutzungsart']['@attributes']['GEWERBE'] + 1;      // y 2=>comercial. Por eso el +1 sobre el 1 de comercial

        $tipo_venta = $datos['anbieter']['immobilie']['objektkategorie']['vermarktungsart']['@attributes']['KAUF'] > 0 ? //si es venta o alquiler.
                $datos['anbieter']['immobilie']['objektkategorie']['vermarktungsart']['@attributes']['KAUF'] : // 1=venta
                $datos['anbieter']['immobilie']['objektkategorie']['vermarktungsart']['@attributes']['MIETE_PACHT'] + 1;      // 2=alquiler

        $tipo_vivienda = isset($datos['anbieter']['immobilie']['objektkategorie']['objektart']['haus']['@attributes']['haustyp']) ?
                $datos['anbieter']['immobilie']['objektkategorie']['objektart']['haus']['@attributes']['haustyp'] :
                $datos['anbieter']['immobilie']['objektkategorie']['objektart'][0]['@attributes']['haustyp'];

        $geo_ubicacion = $datos['anbieter']['immobilie']['geo']['ort'];
        $geo_cordLatitud = $datos['anbieter']['immobilie']['geo']['geokoordinaten']['@attributes']['breitengrad'];
        $geo_cordLongitud = $datos['anbieter']['immobilie']['geo']['geokoordinaten']['@attributes']['laengengrad'];
        $geo_calle = $datos['anbieter']['immobilie']['geo']['strasse'];
        $geo_numero = $datos['anbieter']['immobilie']['geo']['hausnummer'];
        $geo_piso = $datos['anbieter']['immobilie']['geo']['anzahl_etagen'];
        $geo_pais = $datos['anbieter']['immobilie']['geo']['land']['@attributes']['iso_land'];


        $precio_compra = $datos['anbieter']['immobilie']['preise']['kaufpreis'];
        $precio_casa = $datos['anbieter']['immobilie']['preise']['hausgeld'];
        $precio_metro_cuadrado = $datos['anbieter']['immobilie']['preise']['kaufpreis_pro_qm'];
        $precio_tipo_moneda = $datos['anbieter']['immobilie']['preise']['waehrung']['@attributes']['iso_waehrung'];

        $precio_cochera_compraContado = $datos['anbieter']['immobilie']['preise']['stp_carport']['@attributes']['stellplatzkaufpreis'];
        $precio_cochera_compraContado_ext = $datos['anbieter']['immobilie']['preise']['stp_carport']['@attributes']['anzahl'];

        $precio_duplex_compraContado = $datos['anbieter']['immobilie']['preise']['stp_duplex']['@attributes']['stellplatzmiete'];
        $precio_duplex_compraContado_ext = $datos['anbieter']['immobilie']['preise']['stp_duplex']['@attributes']['anzahl'];

        $precio_espacioLibre_compraContado = $datos['anbieter']['immobilie']['preise']['stp_freiplatz']['@attributes']['stellplatzmiete'];
        $precio_espacioLibre_compraContado_ext = $datos['anbieter']['immobilie']['preise']['stp_freiplatz']['@attributes']['anzahl'];

        $precio_garaje_compraContado = $datos['anbieter']['immobilie']['preise']['stp_garage']['@attributes']['stellplatzmiete'];
        $precio_garaje_compraContado_ext = $datos['anbieter']['immobilie']['preise']['stp_garage']['@attributes']['anzahl'];

        $precio_garajePisos_compraContado = $datos['anbieter']['immobilie']['preise']['stp_parkhaus']['@attributes']['stellplatzmiete'];
        $precio_garajePisos_compraContado_ext = $datos['anbieter']['immobilie']['preise']['stp_parkhaus']['@attributes']['anzahl'];

        $precio_aparcamientoSubterraneo_compraContado = $datos['anbieter']['immobilie']['preise']['stp_tiefgarage']['@attributes']['stellplatzmiete'];
        $precio_aparcamientoSubterraneo_compraContado_ext = $datos['anbieter']['immobilie']['preise']['stp_tiefgarage']['@attributes']['anzahl'];

        $precio_otros_compraContado = $datos['anbieter']['immobilie']['preise']['stp_sonstige']['@attributes']['stellplatzmiete'];
        $precio_otros_compraContado_ext = $datos['anbieter']['immobilie']['preise']['stp_sonstige']['@attributes']['anzahl'];

        $subasta = is_array($datos['anbieter']['immobilie']['versteigerung']) ? // que tipo de datos viene aqui?, atributo?, string dentro de...
                "" :
                $datos['anbieter']['immobilie']['versteigerung'];

        $areas_habitable = $datos['anbieter']['immobilie']['flaechen']['wohnflaeche'];
        $areas_util = $datos['anbieter']['immobilie']['flaechen']['nutzflaeche'];
        $areas_terreno = $datos['anbieter']['immobilie']['flaechen']['grundstuecksflaeche'];
        $areas_apartamento = $datos['anbieter']['immobilie']['flaechen']['einliegerwohnung'];

        $areas_numero_sala = $datos['anbieter']['immobilie']['flaechen']['anzahl_zimmer'];
        $areas_numero_habitaciones = $datos['anbieter']['immobilie']['flaechen']['anzahl_schlafzimmer'];
        $areas_numero_wc = $datos['anbieter']['immobilie']['flaechen']['anzahl_badezimmer'];
        $areas_numero_wc2 = $datos['anbieter']['immobilie']['flaechen']['anzahl_sep_wc'];
        $areas_numero_terrazas = $datos['anbieter']['immobilie']['flaechen']['balkon_terrasse_flaeche'];
        $areas_numero_parkings = $datos['anbieter']['immobilie']['flaechen']['anzahl_stellplaetze'];


        //esta tampoco lo tengo muy claro
        $equipo_cocina = isset($datos['anbieter']['immobilie']['ausstattung']['kueche']['@attributes']['EBK']) ? //si no esta definido
                $datos['anbieter']['immobilie']['ausstattung']['kueche']['@attributes']['EBK'] + 1 : //le asigno por defecto NO
                1;                                                                                          //enum(no, si). Si esta definido sera x+1 (1 o 2).
        $equipo_cocina_ebk = $equipo_cocina;

        $equipo_cocina_abierto = isset($datos['anbieter']['immobilie']['ausstattung']['kueche']['@attributes']['OFFEN']) ?
                $datos['anbieter']['immobilie']['ausstattung']['kueche']['@attributes']['OFFEN'] + 1 :
                1;

        //array, string, atributos...
        $equipo_suelo = is_array($datos['anbieter']['immobilie']['ausstattung']['boden']) ?
                "" :
                $datos['anbieter']['immobilie']['ausstattung']['boden'];

        $equipo_chimenea = $datos['anbieter']['immobilie']['ausstattung']['kamin'] + 1;

        $equipo_calefacion_estufa = isset($datos['anbieter']['immobilie']['ausstattung']['heizungsart']['@attributes']['OFEN']) ? //primero evaluamos si esta definido
                $datos['anbieter']['immobilie']['ausstattung']['heizungsart']['@attributes']['OFEN'] + 1 : //y a continuacion si es asi le sumamos
                1;                                                                                                                  //1, porque en la tabla esta
        $equipo_calefacion_suelos = isset($datos['anbieter']['immobilie']['ausstattung']['heizungsart']['@attributes']['ETAGE']) ? //enum('no', 'si'), con lo cual si de   
                $datos['anbieter']['immobilie']['ausstattung']['heizungsart']['@attributes']['ETAGE'] + 1 : //entrada viene 0 se queda a 1, que es no
                1;                                                                                                                  //y si viene 1 sale 2, que es si.
        $equipo_calefacion_central = isset($datos['anbieter']['immobilie']['ausstattung']['heizungsart']['@attributes']['ZENTRAL']) ?
                $datos['anbieter']['immobilie']['ausstattung']['heizungsart']['@attributes']['ZENTRAL'] + 1 :
                1;
        $equipo_calefacion_remota = isset($datos['anbieter']['immobilie']['ausstattung']['heizungsart']['@attributes']['FERN']) ?
                $datos['anbieter']['immobilie']['ausstattung']['heizungsart']['@attributes']['FERN'] + 1 :
                1;
        $equipo_calefacion_piso2 = isset($datos['anbieter']['immobilie']['ausstattung']['heizungsart']['@attributes']['FUSSBODEN']) ?
                $datos['anbieter']['immobilie']['ausstattung']['heizungsart']['@attributes']['FUSSBODEN'] + 1 :
                1;

        $equipo_alumbrado_aceite = isset($datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['OEL']) ? //primero evaluamos si esta definido
                $datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['OEL'] + 1 : //y a continuacion si es asi le sumamos
                1;                                                                                                                  //1, porque en la tabla esta
        $equipo_alumbrado_gas = isset($datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['GAS']) ? //enum('no', 'si'), con lo cual si de  
                $datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['GAS'] + 1 : //entrada viene 0 se queda a 1, que es no
                1;                                                                                                                  //y si viene 1 sale 2, que es si.
        $equipo_alumbrado_electrico = isset($datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['ELEKTRO']) ?
                $datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['ELEKTRO'] + 1 :
                1;
        $equipo_alumbrado_alternativo = isset($datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['ALTERNATIV']) ?
                $datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['ALTERNATIV'] + 1 :
                1;
        $equipo_alumbrado_solar = isset($datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['SOLAR']) ?
                $datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['SOLAR'] + 1 :
                1;
        $equipo_alumbrado_geotermica = isset($datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['ERDWAERME']) ?
                $datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['ERDWAERME'] + 1 :
                1;
        $equipo_alumbrado_aerea = isset($datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['LUFTWP']) ?
                $datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['LUFTWP'] + 1 :
                1;
        $equipo_alumbrado_pellet = isset($datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['PELLET']) ?
                $datos['anbieter']['immobilie']['ausstattung']['befeuerung']['@attributes']['PELLET'] + 1 :
                1;

        $equipo_ac = isset($datos['anbieter']['immobilie']['ausstattung']['klimatisiert']) ?
                $datos['anbieter']['immobilie']['ausstattung']['klimatisiert'] + 1 :
                1;


        $equipo_aparcamiento_exterior_garaje = isset($datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['GARAGE']) ?
                $datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['GARAGE'] + 1 :
                1;
        $equipo_aparcamiento_exterior_garaje2 = isset($datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['TIEFGARAGE']) ?
                $datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['TIEFGARAGE'] + 1 :
                1;
        $equipo_aparcamiento_exterior_cochera = isset($datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['CARPORT']) ?
                $datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['CARPORT'] + 1 :
                1;
        $equipo_aparcamiento_exterior_aireLibre = isset($datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['FREIPLATZ']) ?
                $datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['FREIPLATZ'] + 1 :
                1;
        $equipo_aparcamiento_exterior_parking = isset($datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['PARKHAUS']) ?
                $datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['PARKHAUS'] + 1 :
                1;
        $equipo_aparcamiento_exterior_duplex = isset($datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['DUPLEX']) ?
                $datos['anbieter']['immobilie']['ausstattung']['stellplatzart']['@attributes']['DUPLEX'] + 1 :
                1;




        if (isset($datos['anbieter']['immobilie']['ausstattung']['fahrstuhl']['@attributes']['PERSONEN'])) {
            $equipo_ascensor = 2;
            $equipo_ascensor_personas = $datos['anbieter']['immobilie']['ausstattung']['fahrstuhl']['@attributes']['PERSONEN'];
            $equipo_ascensor_carga = $datos['anbieter']['immobilie']['ausstattung']['fahrstuhl']['@attributes']['LASTEN'];
        } else {
            $equipo_ascensor = 1;
            $equipo_ascensor_personas = "";
            $equipo_ascensor_carga = "";
        }

        $equipo_adaptado_minusvalidos = isset($datos['anbieter']['immobilie']['ausstattung']['rollstuhlgerecht']) ?
                $datos['anbieter']['immobilie']['ausstattung']['rollstuhlgerecht'] + 1 :
                1;
        $equipo_satelite = isset($datos['anbieter']['immobilie']['ausstattung']['kabel_sat_tv']) ?
                $datos['anbieter']['immobilie']['ausstattung']['kabel_sat_tv'] + 1 :
                1;
        $equipo_sin_barreras = isset($datos['anbieter']['immobilie']['ausstattung']['barrierefrei']) ?
                $datos['anbieter']['immobilie']['ausstattung']['barrierefrei'] + 1 :
                1;
        $equipo_sauna = isset($datos['anbieter']['immobilie']['ausstattung']['sauna']) ?
                $datos['anbieter']['immobilie']['ausstattung']['sauna'] + 1 :
                1;
        $equipo_piscina = isset($datos['anbieter']['immobilie']['ausstattung']['swimmingpool']) ?
                $datos['anbieter']['immobilie']['ausstattung']['swimmingpool'] + 1 :
                1;
        $equipo_jardin_invierno = isset($datos['anbieter']['immobilie']['ausstattung']['wintergarten']) ?
                $datos['anbieter']['immobilie']['ausstattung']['wintergarten'] + 1 :
                1;
        $equipo_inst_deportivas = isset($datos['anbieter']['immobilie']['ausstattung']['sporteinrichtungen']) ?
                $datos['anbieter']['immobilie']['ausstattung']['sporteinrichtungen'] + 1 :
                1;
        $equipo_zona_bienestar = isset($datos['anbieter']['immobilie']['ausstattung']['wellnessbereich']) ?
                $datos['anbieter']['immobilie']['ausstattung']['wellnessbereich'] + 1 :
                1;

        $equipo_sotano = isset($datos['anbieter']['immobilie']['ausstattung']['unterkellert']['@attributes']['keller']) ?
                $datos['anbieter']['immobilie']['ausstattung']['unterkellert']['@attributes']['keller'] :
                "";

        $equipo_espacio_bicis = isset($datos['anbieter']['immobilie']['ausstattung']['fahrradraum']) ?
                $datos['anbieter']['immobilie']['ausstattung']['fahrradraum'] + 1 :
                1;

        if (isset($datos['anbieter']['immobilie']['ausstattung']['dachform']['@attributes'])) {
            $equipo_tipo_tejado = implode(",", array_keys($datos['anbieter']['immobilie']['ausstattung']['dachform']['@attributes']));
        } else {
            $equipo_tipo_tejado = "";
        }

        $equipo_biblioteca = isset($datos['anbieter']['immobilie']['ausstattung']['bibliothek']) ?
                $datos['anbieter']['immobilie']['ausstattung']['bibliothek'] + 1 :
                1;
        $equipo_atico = isset($datos['anbieter']['immobilie']['ausstattung']['dachboden']) ?
                $datos['anbieter']['immobilie']['ausstattung']['dachboden'] + 1 :
                1;
        $equipo_aseo_invitados = isset($datos['anbieter']['immobilie']['ausstattung']['gaestewc']) ?
                $datos['anbieter']['immobilie']['ausstattung']['gaestewc'] + 1 :
                1;
        $equipo_personas_mayores = isset($datos['anbieter']['immobilie']['ausstattung']['seniorengerecht']) ?
                $datos['anbieter']['immobilie']['ausstattung']['seniorengerecht'] + 1 :
                1;


        $estado_construccion = isset($datos['anbieter']['immobilie']['zustand_angaben']['baujahr']) ?
                $datos['anbieter']['immobilie']['zustand_angaben']['baujahr'] :
                "";

        $estado_estado = isset($datos['anbieter']['immobilie']['zustand_angaben']['zustand']['@attributes']['zustand_art']) ?
                $datos['anbieter']['immobilie']['zustand_angaben']['zustand']['@attributes']['zustand_art'] :
                "";
        $estado_energetico_fgeewert = isset($datos['anbieter']['immobilie']['zustand_angaben']['energiepass']['fgeewert']) ?
                $datos['anbieter']['immobilie']['zustand_angaben']['energiepass']['fgeewert'] :
                "";
        $estado_energetico_epart = isset($datos['anbieter']['immobilie']['zustand_angaben']['energiepass']['epart']) ?
                $datos['anbieter']['immobilie']['zustand_angaben']['energiepass']['epart'] :
                "";

//supongo que el siguiente es ok.
        $estado_energetico_valido = is_array($datos['anbieter']['immobilie']['zustand_angaben']['energiepass']['gueltig_bis']) ? //enum (1="si", 2="no")
                2 :
                $datos['anbieter']['immobilie']['zustand_angaben']['energiepass']['gueltig_bis'];

        $estado_energetico_demanda = isset($datos['anbieter']['immobilie']['zustand_angaben']['energiepass']['endenergiebedarf']) ?
                $datos['anbieter']['immobilie']['zustand_angaben']['energiepass']['endenergiebedarf'] :
                "";


        $estado_venta = isset($datos['anbieter']['immobilie']['zustand_angaben']['verkaufstatus']['@attributes']['stand']) ?
                $datos['anbieter']['immobilie']['zustand_angaben']['verkaufstatus']['@attributes']['stand'] :
                "";

        $valoracion = is_array($datos['anbieter']['immobilie']['bewertung']) ?
                "" :
                $datos['anbieter']['immobilie']['bewertung'];



        $infraestructura_distancia_escuela = isset($datos['anbieter']['immobilie']['distanzen'][0]) ?
                $datos['anbieter']['immobilie']['distanzen'][0] :
                0;

        $infraestructura_distancia_instituto = isset($datos['anbieter']['immobilie']['distanzen'][1]) ?
                $datos['anbieter']['immobilie']['distanzen'][1] :
                0;
        $infraestructura_distancia_aereopuerto = isset($datos['anbieter']['immobilie']['distanzen'][2]) ?
                $datos['anbieter']['immobilie']['distanzen'][2] :
                0;
        $infraestructura_distancia_estacionRemota = isset($datos['anbieter']['immobilie']['distanzen'][3]) ?
                $datos['anbieter']['immobilie']['distanzen'][3] :
                0;
        $infraestructura_distancia_autovia = isset($datos['anbieter']['immobilie']['distanzen'][4]) ?
                $datos['anbieter']['immobilie']['distanzen'][4] :
                0;
        $infraestructura_distancia_tren = isset($datos['anbieter']['immobilie']['distanzen'][5]) ?
                $datos['anbieter']['immobilie']['distanzen'][5] :
                0;
        $infraestructura_distancia_bus = isset($datos['anbieter']['immobilie']['distanzen'][6]) ?
                $datos['anbieter']['immobilie']['distanzen'][6] :
                0;
        $infraestructura_distancia_restaurante = isset($datos['anbieter']['immobilie']['distanzen'][7]) ?
                $datos['anbieter']['immobilie']['distanzen'][7] :
                0;
        $infraestructura_distancia_centro = isset($datos['anbieter']['immobilie']['distanzen'][8]) ?
                $datos['anbieter']['immobilie']['distanzen'][8] :
                0;



        $extra_titulo = is_array($datos['anbieter']['immobilie']['freitexte']['objekttitel']) ?
                "" :
                $datos['anbieter']['immobilie']['freitexte']['dreizeiler'];
        $extra_ziler = is_array($datos['anbieter']['immobilie']['freitexte']['dreizeiler']) ?
                "" :
                $datos['anbieter']['immobilie']['freitexte']['dreizeiler'];
        $extra_ubicacion = is_array($datos['anbieter']['immobilie']['freitexte']['lage']) ?
                "" :
                $datos['anbieter']['immobilie']['freitexte']['lage'];
        $extra_restriccion_ausstatt = isset($datos['anbieter']['immobilie']['freitexte']['ausstatt_beschr']) ?
                $datos['anbieter']['immobilie']['freitexte']['ausstatt_beschr'] :
                "";
        $extra_descripcion = isset($datos['anbieter']['immobilie']['freitexte']['objektbeschreibung']) ?
                $datos['anbieter']['immobilie']['freitexte']['objektbeschreibung'] :
                "";
        $extra_otras = isset($datos['anbieter']['immobilie']['freitexte']['sonstige_angaben']) ?
                $datos['anbieter']['immobilie']['freitexte']['sonstige_angaben'] :
                "";
        $inmueble_xml_id = $datos['anbieter']['immobilie']['verwaltung_techn']['openimmo_obid'];


        $query = "UPDATE INMUEBLE SET 
                `uso` = '$uso',
                `tipo_venta` = '$tipo_venta',
                `tipo_vivienda` = '$tipo_vivienda',
                `geo_ubicacion` = '$geo_ubicacion',
                `geo_cordLatitud` = '$geo_cordLatitud',
                `geo_cordLongitud` = '$geo_cordLongitud',
                `geo_calle` = '$geo_calle',
                `geo_numero` = '$geo_numero',
                `geo_piso` = '$geo_piso',
                `geo_pais` = '$geo_pais',
                `precio_compra` = '$precio_compra',
                `precio_casa` = '$precio_casa',
                `precio_metro_cuadrado` = '$precio_metro_cuadrado',
                `precio_tipo_moneda` = '$precio_tipo_moneda',
                `precio_cochera_compraContado` = '$precio_cochera_compraContado',
                `precio_cochera_compraContado_ext` = '$precio_cochera_compraContado_ext',
                `precio_duplex_compraContado` = '$precio_duplex_compraContado',
                `precio_duplex_compraContado_ext` = '$precio_duplex_compraContado_ext',
                `precio_espacioLibre_compraContado` = '$precio_espacioLibre_compraContado',
                `precio_espacioLibre_compraContado_ext` = '$precio_espacioLibre_compraContado_ext',
                `precio_garaje_compraContado` = '$precio_garaje_compraContado',
                `precio_garaje_compraContado_ext` = '$precio_garaje_compraContado_ext',
                `precio_garajePisos_compraContado` = '$precio_garajePisos_compraContado',
                `precio_garajePisos_compraContado_ext` = '$precio_garajePisos_compraContado_ext',
                `precio_aparcamientoSubterraneo_compraContado` = '$precio_aparcamientoSubterraneo_compraContado',
                `precio_aparcamientoSubterraneo_compraContado_ext` = '$precio_aparcamientoSubterraneo_compraContado_ext',
                `precio_otros_compraContado` = '$precio_otros_compraContado',
                `precio_otros_compraContado_ext` = '$precio_otros_compraContado_ext',
                `subasta` = '$subasta',
                `areas_habitable` = '$areas_habitable',
                `areas_util` = '$areas_util',
                `areas_terreno` = '$areas_terreno',
                `areas_apartamento` = '$areas_apartamento',
                `areas_numero_sala` = '$areas_numero_sala',
                `areas_numero_habitaciones` = '$areas_numero_habitaciones',
                `areas_numero_wc` = '$areas_numero_wc',
                `areas_numero_wc2` = '$areas_numero_wc2',
                `areas_numero_terrazas` = '$areas_numero_terrazas',
                `areas_numero_parkings` = '$areas_numero_parkings',
                `equipo_cocina` = '$equipo_cocina',
                `equipo_cocina_ebk` = '$equipo_cocina_ebk',
                `equipo_cocina_abierto` = '$equipo_cocina_abierto',
                `equipo_suelo` = '$equipo_suelo',
                `equipo_chimenea` = '$equipo_chimenea',
                `equipo_calefacion_estufa` = '$equipo_calefacion_estufa',
                `equipo_calefacion_suelos` = '$equipo_calefacion_suelos',
                `equipo_calefacion_central` = '$equipo_calefacion_central',
                `equipo_calefacion_remota` = '$equipo_calefacion_remota',
                `equipo_calefacion_piso2` = '$equipo_calefacion_piso2',
                `equipo_alumbrado_aceite` = '$equipo_alumbrado_aceite',
                `equipo_alumbrado_gas` = '$equipo_alumbrado_gas',
                `equipo_alumbrado_electrico` = '$equipo_alumbrado_electrico',
                `equipo_alumbrado_alternativo` = '$equipo_alumbrado_alternativo',
                `equipo_alumbrado_solar` = '$equipo_alumbrado_solar',
                `equipo_alumbrado_geotermica` = '$equipo_alumbrado_geotermica',
                `equipo_alumbrado_aerea` = '$equipo_alumbrado_aerea',
                `equipo_alumbrado_pellet` = '$equipo_alumbrado_pellet',

                `equipo_aparcamiento_exterior_garaje` = '$equipo_aparcamiento_exterior_garaje',
                `equipo_aparcamiento_exterior_garaje` = '$equipo_aparcamiento_exterior_garaje2',
                `equipo_aparcamiento_exterior_garaje` = '$equipo_aparcamiento_exterior_cochera',
                `equipo_aparcamiento_exterior_garaje` = '$equipo_aparcamiento_exterior_aireLibre',
                `equipo_aparcamiento_exterior_garaje` = '$equipo_aparcamiento_exterior_parking',
                `equipo_aparcamiento_exterior_garaje` = '$equipo_aparcamiento_exterior_duplex',

                `equipo_ac` = '$equipo_ac',
                `equipo_ascensor` = '$equipo_ascensor',
                `equipo_ascensor_personas` = '$equipo_ascensor_personas',
                `equipo_ascensor_carga` = '$equipo_ascensor_carga',
                `equipo_adaptado_minusvalidos` = '$equipo_adaptado_minusvalidos',
                `equipo_satelite` = '$equipo_satelite',
                `equipo_sin_barreras` = '$equipo_sin_barreras',
                `equipo_sauna` = '$equipo_sauna',
                `equipo_piscina` = '$equipo_piscina',
                `equipo_jardin_invierno` = '$equipo_jardin_invierno',
                `equipo_inst_deportivas` = '$equipo_inst_deportivas',
                `equipo_zona_bienestar` = '$equipo_zona_bienestar',
                `equipo_sotano` = '$equipo_sotano',
                `equipo_espacio_bicis` = '$equipo_espacio_bicis',
                `equipo_tipo_tejado` = '$equipo_tipo_tejado',
                `equipo_biblioteca` = '$equipo_biblioteca',
                `equipo_atico` = '$equipo_atico',
                `equipo_aseo_invitados` = '$equipo_aseo_invitados',
                `equipo_personas_mayores` = '$equipo_personas_mayores',
                `estado_construccion` = '$estado_construccion',
                `estado_estado` = '$estado_estado',
                `estado_energetico_fgeewert` = '$estado_energetico_fgeewert',
                `estado_energetico_epart` = '$estado_energetico_epart',
                `estado_energetico_valido` = '$estado_energetico_valido',
                `estado_energetico_demanda` = '$estado_energetico_demanda',
                `estado_venta` = '$estado_venta',
                `valoracion` = '$valoracion',
                `infraestructura_distancia_escuela` = '$infraestructura_distancia_escuela',
                `infraestructura_distancia_instituto` = '$infraestructura_distancia_instituto',
                `infraestructura_distancia_aereopuerto` = '$infraestructura_distancia_aereopuerto',
                `infraestructura_distancia_estacionRemota` = '$infraestructura_distancia_estacionRemota',
                `infraestructura_distancia_autovia` = '$infraestructura_distancia_autovia',
                `infraestructura_distancia_tren` = '$infraestructura_distancia_tren',
                `infraestructura_distancia_bus` = '$infraestructura_distancia_bus',
                `infraestructura_distancia_restaurante` = '$infraestructura_distancia_restaurante',
                `infraestructura_distancia_centro` = '$infraestructura_distancia_centro',
                `extra_titulo` = '$extra_titulo',
                `extra_ziler` = '$extra_ziler',
                `extra_ubicacion` = '$extra_ubicacion',
                `extra_restriccion_ausstatt` = '$extra_restriccion_ausstatt',
                `extra_descripcion` = '$extra_descripcion',
                `extra_otras` = '$extra_otras',
                `json_total` = '" . $this->datos_json . "'
            WHERE `inmueble_xml_id`='$inmueble_xml_id'";


        if ($this->mysqlHandler->executeQuery($query)) {

            //obtenemos el id del inmueble actualizado.
            $query = "SELECT id FROM inmueble WHERE `inmueble_xml_id`='$inmueble_xml_id'";
            $resource = $this->mysqlHandler->executeQuery($query);

            $datosInmueble = mysql_fetch_assoc($resource);
            
            $inmuebleId = $datosInmueble['id'];

            //eliminamos todas las imagenes antiguas? o añadimos las nuevas y listo.

            $arrayTraduccionTiposImagenes = array("TITELBILD" => 1, "BILD" => 2, "KARTEN_LAGEPLAN" => 3);

            //procedemos a registrar las imagenes.                
            $queryInsertValues = "";

            foreach ($datos['anbieter']['immobilie']['anhaenge']['anhang'] as $fotosArray) {

                $tipo = $arrayTraduccionTiposImagenes[$fotosArray['@attributes']['gruppe']];
                $titulo = $fotosArray['anhangtitel'];
                //garantizamos que sea unico el nombre del fichero donde esta la foto.
                $ficheroPath = $inmuebleId . "_" . substr(sha1($fotosArray['daten']['pfad']), 0, 9) . "." . $fotosArray['format'];

                $queryInsertValues .= "(NULL, '$inmuebleId', '$tipo', '$titulo', '$ficheroPath'), ";

                //este atributo contiene el array relacional entre el nombre antiguo de las imagenes y su nuevo nombre.
                $this->imgFilesCorrelation[$fotosArray['daten']['pfad']] = $ficheroPath;
            }
            $queryInsertValues = trim($queryInsertValues, ", ");

            $this->mysqlHandler->executeQuery("INSERT INTO inmueble_img(id,inmueble_id,tipo,titulo,fichero) VALUES $queryInsertValues");
        }
    }

    private function delete() {
        $query = "DELETE FROM inmueble WHERE inmueble_xml_id='" . $this->datos['anbieter']['immobilie']['verwaltung_techn']['openimmo_obid'] . "'";
        $this->mysqlHandler->executeQuery($query);
    }

    /**
     * funcion para ejecutar la accion asociada en el fichero xml. Previamente se debio haber llamado al metodo loadFromFile;
     * @return boolean devuelve true si se pudo ejecutar la funcion o false si ocurrio algun error.
     */
    public function commit() {
        if (!$this->datos) {
            return false;
        }

        //analizamos que tipo de datos es para realizar una u otra operación.
        $modo = $this->datos['uebertragung']['@attributes']['modus'];

        switch ($modo) {
            case "CHANGE":
                //comprobamos si existe en la bd un inmueble con el id de inmueble del xml
                $query = "SELECT id FROM inmueble WHERE inmueble_xml_id='" . $this->datos['anbieter']['immobilie']['verwaltung_techn']['openimmo_obid'] . "'";
                $tArray = mysql_fetch_assoc( $this->mysqlHandler->executeQuery($query) );

                if (isset($tArray['id'])) {
                    //es un update

                    $this->update();
                } else {
                    //es un insert
                    return $this->insert();
                }

                break;

            case "DELETE":
                $this->delete();
                break;

            default:
                break;
        }
    }

    /**
     * Funcion para realizar la carga de datos. SE le pasa un fichero xml con el formato debido y a partir de ahi
     * se obtienen los datos y se procede a realizar el insert mediante el objeto encargado de la gestion de la base de datos
     * con el que se instancion el objeto en el constructor.
     * 
     * @param string $filePath string con la ruta al fichero para realizar la carga de datos.
     * @return mixed la funcion devuelve false si hubo un error o si el fichero no existe.
     */
    public function loadFromFile($filePath) {
        if (!is_file($filePath)) {
            return false;
        }

        $this->datos_json = json_encode((array) simplexml_load_string(file_get_contents($filePath)));
        $this->datos = json_decode($this->datos_json, true);
    }

    public function getimgFilesCorrelation() {
        return $this->imgFilesCorrelation;
    }

    public function getDatos() {
        return $this->datos;
    }

}

?>