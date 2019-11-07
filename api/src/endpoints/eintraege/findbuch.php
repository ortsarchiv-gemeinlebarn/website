<?php

    $app->get('/v1/findbuch/{display_type}/{range_type}', function ($request, $response, $args) {

        $findbuch_version = "1.3";
        $ids = array();
        $eintraege = array();
        error_reporting(0);


        // get Einträge

        if ($args['range_type'] == 'alle'){

            $sth = $this->db->prepare(
                "SELECT 
                    eintraege.id AS eintraege_id,
                    LPAD(eintraege.id, 6, '0') AS eintraege_id_formatted,
                    bestaende.id AS bestaende_id,
                    bestaende.signatur AS bestaende_signatur,
                    eintraege.titel AS eintraege_titel,
                    eintraege.kommentar AS eintraege_kommentar,
                    eintraege.zeit_von AS datierung_von_ymd,
                    DATE_FORMAT(eintraege.zeit_von, '%d.%m.%Y') AS datierung_von_dmy,
                    UNIX_TIMESTAMP(eintraege.zeit_von) AS datierung_von_unix,
                    eintraege.zeit_bis AS datierung_bis_ymd,
                    DATE_FORMAT(eintraege.zeit_bis, '%d.%m.%Y') AS datierung_bis_dmy,
                    UNIX_TIMESTAMP(eintraege.zeit_bis) AS datierung_bis_unix,
                    eintraege.zeit_text AS datierung_text,
                    eintraege.urheber_name,
                    eintraege.urheber_infos,
                    eintraege.urheber_verstaendnis_eingeholt,
                    eintraege.urheber_sperrfrist_erloschen,
                    eintraege.tags_hauptobjekte,
                    eintraege.tags_nebenobjekte,
                    eintraege.tags_personen,
                    medium_unterkategorien.id AS medium_unterkategorien_id,
                    medium_unterkategorien.titel AS medium_unterkategorien_titel,
                    medium_hauptkategorien.id AS medium_hauptkategorien_id,
                    medium_hauptkategorien.titel AS medium_hauptkategorien_titel,
                    thema_unterkategorien.id AS thema_unterkategorien_id,
                    thema_unterkategorien.titel AS thema_unterkategorien_titel,
                    thema_hauptkategorien.id AS thema_hauptkategorien_id,
                    thema_hauptkategorien.titel AS thema_hauptkategorien_titel,
                    DATE_FORMAT(eintraege.datum_bearbeitet, '%d.%m.%Y %H:%i:%s') AS datum_bearbeitet_dmy
                FROM 
                    eintraege 
                LEFT JOIN 
                    bestaende ON bestaende.id = eintraege.bestaende_id
                LEFT JOIN 
                    medium_unterkategorien ON medium_unterkategorien.id = eintraege.medium_unterkategorien_id
                LEFT JOIN
                    medium_hauptkategorien ON medium_hauptkategorien.id = medium_unterkategorien.medium_hauptkategorien_id
                LEFT JOIN 
                    thema_unterkategorien ON thema_unterkategorien.id = eintraege.thema_unterkategorien_id
                LEFT JOIN
                    thema_hauptkategorien ON thema_hauptkategorien.id = thema_unterkategorien.thema_hauptkategorien_id
                ORDER BY 
                    bestaende.id, eintraege.id"
            
            );

            $sth->bindParam(':bestaende_id', $bestaende_id, PDO::PARAM_INT);
            $sth->execute();

            $eintraege = array_merge($eintraege, $sth->fetchAll());

        }else if ($args['range_type'] == 'ids'){

            for($i=0;$i<3;$i++){

                $ids = array();

                if ($i==0){
                    $param_ids = $request->getParam('range_GOA');
                    $bestaende_signatur = 'GOA';
                    $bestaende_id = 1;
                } else if ($i==1){
                    $param_ids = $request->getParam('range_GFA');
                    $bestaende_signatur = 'GFA';
                    $bestaende_id = 2;
                } else if ($i==2){
                    $param_ids = $request->getParam('range_GSA');
                    $bestaende_signatur = 'GSA';
                    $bestaende_id = 3;
                }
                
                $ids_noblanks = str_replace(" ", "", $param_ids);
                $ids_parts = explode(",", $ids_noblanks);
        
                foreach($ids_parts as $id_part){
                    if (strpos($id_part, "-")){
                        $start = explode("-", $id_part)[0];
                        $ende = explode("-", $id_part)[1];
        
                        for($j=$start;$j<=$ende;$j++){
                            array_push($ids, intval($j));
                        }
                    }else{
                        array_push($ids, intval($id_part));
                    }
                }
        
                $id_arr_str = "(" . implode(", ", $ids) . ")";
        
                $sth = $this->db->prepare(
                    "SELECT 
                        eintraege.id AS eintraege_id,
                        LPAD(eintraege.id, 6, '0') AS eintraege_id_formatted,
                        bestaende.id AS bestaende_id,
                        bestaende.signatur AS bestaende_signatur,
                        eintraege.titel AS eintraege_titel,
                        eintraege.kommentar AS eintraege_kommentar,
                        eintraege.zeit_von AS datierung_von_ymd,
                        DATE_FORMAT(eintraege.zeit_von, '%d.%m.%Y') AS datierung_von_dmy,
                        UNIX_TIMESTAMP(eintraege.zeit_von) AS datierung_von_unix,
                        eintraege.zeit_bis AS datierung_bis_ymd,
                        DATE_FORMAT(eintraege.zeit_bis, '%d.%m.%Y') AS datierung_bis_dmy,
                        UNIX_TIMESTAMP(eintraege.zeit_bis) AS datierung_bis_unix,
                        eintraege.zeit_text AS datierung_text,
                        eintraege.urheber_name,
                        eintraege.urheber_infos,
                        eintraege.urheber_verstaendnis_eingeholt,
                        eintraege.urheber_sperrfrist_erloschen,
                        eintraege.tags_hauptobjekte,
                        eintraege.tags_nebenobjekte,
                        eintraege.tags_personen,
                        medium_unterkategorien.id AS medium_unterkategorien_id,
                        medium_unterkategorien.titel AS medium_unterkategorien_titel,
                        medium_hauptkategorien.id AS medium_hauptkategorien_id,
                        medium_hauptkategorien.titel AS medium_hauptkategorien_titel,
                        thema_unterkategorien.id AS thema_unterkategorien_id,
                        thema_unterkategorien.titel AS thema_unterkategorien_titel,
                        thema_hauptkategorien.id AS thema_hauptkategorien_id,
                        thema_hauptkategorien.titel AS thema_hauptkategorien_titel,
                        DATE_FORMAT(eintraege.datum_bearbeitet, '%d.%m.%Y %H:%i:%s') AS datum_bearbeitet_dmy
                    FROM 
                        eintraege 
                    LEFT JOIN 
                        bestaende ON bestaende.id = eintraege.bestaende_id
                    LEFT JOIN 
                        medium_unterkategorien ON medium_unterkategorien.id = eintraege.medium_unterkategorien_id
                    LEFT JOIN
                        medium_hauptkategorien ON medium_hauptkategorien.id = medium_unterkategorien.medium_hauptkategorien_id
                    LEFT JOIN 
                        thema_unterkategorien ON thema_unterkategorien.id = eintraege.thema_unterkategorien_id
                    LEFT JOIN
                        thema_hauptkategorien ON thema_hauptkategorien.id = thema_unterkategorien.thema_hauptkategorien_id
                    WHERE 
                        eintraege.bestaende_id = :bestaende_id AND
                        eintraege.id IN {$id_arr_str}
                    ORDER BY 
                        eintraege.id"
                
                );

                $sth->bindParam(':bestaende_id', $bestaende_id, PDO::PARAM_INT);
                $sth->execute();

                $eintraege = array_merge($eintraege, $sth->fetchAll());
                
            }
        }

        for ($i=0;$i<count($eintraege);$i++){

            $sth = $this->db->prepare(
                "SELECT 
                    items_physisch.id AS id,
                    originalitaeten.id AS originalitaeten_id,
                    originalitaeten.titel AS originalitaeten_titel,
                    farbraeume.id AS farbraeume_id,
                    farbraeume.titel AS farbraeume_titel,
                    items_physisch.groesse,
                    items_physisch.besitzer_goa,
                    behaeltnisse.id AS behaeltnisse_id,
                    LPAD(behaeltnisse.id, 3, '0') AS behaeltnisse_id_formatted,
                    behaeltnisse.name AS behaeltnisse_name,
                    behaeltnisse.inhalt AS behaeltnisse_inhalt,
                    behaeltnisse_bestaende.id AS behaeltnisse_bestaende_id,
                    behaeltnisse_bestaende.signatur AS behaeltnisse_bestaende_signatur,
                    items_physisch.quellstueck
                FROM 
                    items_physisch 
                LEFT JOIN 
                    farbraeume ON farbraeume.id = items_physisch.farbraeume_id
                LEFT JOIN 
                    behaeltnisse ON behaeltnisse.id = items_physisch.behaeltnisse_id AND behaeltnisse.bestaende_id = items_physisch.behaeltnisse_bestaende_id 
                LEFT JOIN 
                    bestaende AS behaeltnisse_bestaende ON behaeltnisse_bestaende.id = behaeltnisse.bestaende_id
                LEFT JOIN 
                    bestaende ON bestaende.id = items_physisch.eintraege_bestaende_id
                LEFT JOIN 
                    originalitaeten ON originalitaeten.id = items_physisch.originalitaeten_id
                WHERE 
                    bestaende.signatur = :bestaende_signatur AND items_physisch.eintraege_id = :id AND items_physisch.besitzer_goa=1"
            );

            $sth->bindParam(':bestaende_signatur', $eintraege[$i]['bestaende_signatur'], PDO::PARAM_STR);
            $sth->bindParam(':id', $eintraege[$i]['eintraege_id'], PDO::PARAM_INT);
            $sth->execute();
            $eintraege[$i]['items_physisch_archiv'] = $sth->fetchAll();

            $sth = $this->db->prepare(
                "SELECT 
                    items_physisch.id AS id,
                    originalitaeten.id AS originalitaeten_id,
                    originalitaeten.titel AS originalitaeten_titel,
                    farbraeume.id AS farbraeume_id,
                    farbraeume.titel AS farbraeume_titel,
                    items_physisch.groesse,
                    items_physisch.besitzer_goa,
                    items_physisch.besitzer_extern_name,
                    items_physisch.besitzer_extern_geburtsdatum AS besitzer_extern_geburtsdatum_ymd,
                    DATE_FORMAT(items_physisch.besitzer_extern_geburtsdatum, '%d.%m.%Y') AS besitzer_extern_geburtsdatum_dmy,
                    UNIX_TIMESTAMP(items_physisch.besitzer_extern_geburtsdatum) AS besitzer_extern_geburtsdatum_unix,
                    items_physisch.besitzer_extern_adresse,
                    items_physisch.besitzer_extern_plz,
                    items_physisch.besitzer_extern_ort,
                    items_physisch.besitzer_extern_email,
                    items_physisch.besitzer_extern_telefon,
                    items_physisch.besitzer_extern_plz,
                    items_physisch.quellstueck
                FROM 
                    items_physisch 
                LEFT JOIN 
                    farbraeume ON farbraeume.id = items_physisch.farbraeume_id
                LEFT JOIN 
                    bestaende ON bestaende.id = items_physisch.eintraege_bestaende_id
                LEFT JOIN 
                    originalitaeten ON originalitaeten.id = items_physisch.originalitaeten_id
                WHERE 
                    bestaende.signatur = :bestaende_signatur AND items_physisch.eintraege_id = :id AND items_physisch.besitzer_goa=0"
            );

            $sth->bindParam(':bestaende_signatur', $eintraege[$i]['bestaende_signatur'], PDO::PARAM_STR);
            $sth->bindParam(':id', $eintraege[$i]['eintraege_id'], PDO::PARAM_INT);
            $sth->execute();
            $eintraege[$i]['items_physisch_extern'] = $sth->fetchAll();

            $sth = $this->db->prepare(
                "SELECT 
                    items_digital.id,
                    items_digital.eintraege_id,
                    items_digital.lfnr,
                    filetypen.id AS filetypen_id,
                    filetypen.titel AS filetypen_titel,
                    items_digital.dpi,
                    items_digital.nachbearbeitet,
                    items_digital.beschreibung,
                    items_digital.datum AS datum_ymd,
                    DATE_FORMAT(items_digital.datum, '%d.%m.%Y') AS datum_dmy,
                    UNIX_TIMESTAMP(items_digital.datum) AS datum_unix,
                    items_physisch.id AS items_physisch_link_id,
                    items_physisch.besitzer_goa AS items_physisch_link_besitzer_goa
                FROM 
                    items_digital 
                LEFT JOIN 
                    items_physisch ON items_physisch.id = items_digital.items_physisch_link
                LEFT JOIN 
                    filetypen ON filetypen.id = items_digital.filetypen_id
                LEFT JOIN 
                    bestaende ON bestaende.id = items_digital.eintraege_bestaende_id
                WHERE 
                    bestaende.signatur = :bestaende_signatur AND items_digital.eintraege_id = :id"
            );

            $sth->bindParam(':bestaende_signatur', $eintraege[$i]['bestaende_signatur'], PDO::PARAM_STR);
            $sth->bindParam(':id', $eintraege[$i]['eintraege_id'], PDO::PARAM_INT);
            $sth->execute();
            $eintraege[$i]['items_digital_archiv'] = $sth->fetchAll();
        }



        // PDF

        require_once(__DIR__ . '/../../vendor/GOA_PDF.php');

        $pdf = new GOA_PDF('P','mm','A4');
        $pdf->SetAutoPageBreak(false);
        $pdf->AliasNbPages('{totalPages}');

        $pdf->AddFont('JosefinLight','','JosefinSans-Light.php');
        $pdf->AddFont('JosefinLight','I','JosefinSans-LightItalic.php');
        $pdf->AddFont('Josefin','','JosefinSans-Regular.php');
        $pdf->AddFont('JosefinSemiBold','B','JosefinSans-SemiBold.php');
        $pdf->AddFont('Josefin','B','JosefinSans-Bold.php');

        $page_padding_left = 15.00;
        $page_padding_top = 15.00;
        $page_padding_right = 15.00;
        $page_padding_bottom = 22.00;
        $page_footer_height = 5.00;
        $page_footer_margin = 2.00;
        $border = 0;

        $font_size_list = 8;
        $padding_cell = 1.5;

        $date_generiert = time();

        $archivtitel = "Ortsarchiv Gemeinlebarn";
        $meta_informationen = "Findbuch Version: {$findbuch_version}, Export erstellt: " . date('d.m.Y H:i:s', $date_generiert);

        if ($args['display_type'] == 'list'){
        
            $page_width = 297.00;
            $page_height = 210.00;

            $pdf->SetLineWidth(0.15);
            $pdf->SetDrawColor(230,230,230);

            $newpage = true;
            $firstpage = true;
            $header_row_height = 10;
            $row_line_height = 5;
            $firstpage_top = 100;

            $metrics = array(
                array(
                    "width"  => 22,
                    "titel"  => "Bestand / ID",
                    "align"  => "L"
                ),
                array(
                    "width"  => 80,
                    "titel"  => "Titel",
                    "align"  => "L"
                ),
                array(
                    "width"  => 27,
                    "titel"  => "Datierung",
                    "align"  => "L"
                ),
                array(
                    "width"  => 76,
                    "titel"  => "Thema Kategorisierung",
                    "align"  => "L"
                ),
                array(
                    "width"  => 38,
                    "titel"  => "Medium Kategorisierung",
                    "align"  => "L"
                ),
                array(
                    "width"  => 8,
                    "titel"  => "P/A",
                    "align"  => "C"
                ),
                array(
                    "width"  => 8,
                    "titel"  => "D/A",
                    "align"  => "C"
                ),
                array(
                    "width"  => 8,
                    "titel"  => "P/E",
                    "align"  => "C"
                )
            );

            // First Page Header

            $pdf->AddPage('L');

            $x = $page_padding_left;
            $y = $page_padding_top + 30;

            $pdf->SetTextColor(0,0,0);
            $pdf->SetFont('Josefin','B', 35);
            $pdf->SetXY($x, $y);
            $pdf->Cell($page_width - $page_padding_left - $page_padding_right, 18, iconv('UTF-8', 'windows-1252', "Findbuch Export"), $border, NULL, 'L');
            
            $pdf->SetTextColor(100,100,100);
            $pdf->SetFont('JosefinLight','', 12);
            $pdf->SetXY($x, $y+20);
            $pdf->Cell($page_width - $page_padding_left - $page_padding_right, 10, iconv('UTF-8', 'windows-1252', "{$archivtitel} | Erstellt: ".date('d.m.Y H:i:s', $date_generiert)), $border, NULL, 'L');
            $pdf->SetXY($x, $y+32);
            $pdf->Cell($page_width - $page_padding_left - $page_padding_right, 10, iconv('UTF-8', 'windows-1252', "Übersichtsliste | " . count($eintraege) . " Einträge | {totalPages} Seiten"), $border, NULL, 'L');
            
            foreach($eintraege as $e){

                // Page
                if ($newpage){

                    $x = $page_padding_left;

                    if ($firstpage){
                        $firstpage = false;
                        $y = $page_padding_top + $firstpage_top;
                    }else{
                        $y = $page_padding_top + $padding_cell;
                        $pdf->AddPage('L');
                        $firstpage_top = 0;
                    }

                    if ($border) $pdf->Rect($page_padding_left, $page_padding_top, $page_width - $page_padding_left - $page_padding_right, $page_height - $page_padding_top - $page_padding_bottom, 'D');

                    // Fußzeile
                    $pdf->SetTextColor(100,100,100);
                    $pdf->SetFont('JosefinLight','I', $font_size_list);

                    $field_width = ($page_width - $page_padding_left - $page_padding_right)/3;

                    $pdf->SetXY($page_padding_left, $page_height + $page_footer_margin - $page_padding_bottom);
                    $pdf->Cell($field_width, $page_footer_height, iconv('UTF-8', 'windows-1252', $archivtitel), $border, NULL, 'L');
                    $pdf->Cell($field_width, $page_footer_height, iconv('UTF-8', 'windows-1252', $pdf->PageNo()." / {totalPages}"), $border, NULL, 'C');
                    $pdf->Cell($field_width, $page_footer_height, iconv('UTF-8', 'windows-1252', $meta_informationen), $border, NULL, 'R');

                    // Kopfzeile
                    $pdf->setFillColor(248, 248, 248);
                    $pdf->Rect(-1, $y - $padding_cell, $page_width+2, $header_row_height + 2*$padding_cell, 'FD');
                    for($j=0;$j < count($metrics);$j++){
                        $pdf->SetTextColor(30,30,30);
                        $pdf->SetFont('Josefin','B', $font_size_list);
                        $pdf->SetXY($x, $y);
                        $pdf->Cell($metrics[$j]['width'], $header_row_height, iconv('UTF-8', 'windows-1252', $metrics[$j]['titel']), $border, NULL, $metrics[$j]['align']);

                        //$pdf->Rect($x, $y - $padding_cell, $metrics[$j]['width'], $header_row_height + 2* $padding_cell, 'D');

                        $x = $x + $metrics[$j]['width'];
                    }
                    
                    $pdf->SetXY($x, $y);
                    $newpage = false;
                    $last_row_height = $header_row_height + $padding_cell;
                    $y = $page_padding_top + $firstpage_top + 2*$padding_cell;
                    $row = 1;

                    $pdf->SetTextColor(30,30,30);
                    $pdf->SetFont('Josefin','', $font_size_list);
                }

                $x = $page_padding_left;
                $y = $y + $last_row_height;
                $calc_row_height = $row_line_height + 2*$padding_cell;

                $bestand_logo_height = 3.333;
                $bestand_logo_width  = 6.00;
                $pdf->Image(__DIR__ . "/../../vendor/{$e['bestaende_signatur']}.png", $x + $padding_cell, $y - $padding_cell + ($calc_row_height/2) - ($bestand_logo_height/2) , $bestand_logo_width, $bestand_logo_height);
                
                for($j=0;$j < count($metrics);$j++){

                    switch ($j){
                        case 0: $str = "          {$e['eintraege_id_formatted']}";
                            break;
                        case 1: $str = $e['eintraege_titel'];
                            break;
                        case 2: $str = $e['datierung_text'];
                            break;
                        case 3: $str = "{$e['thema_hauptkategorien_titel']} > {$e['thema_unterkategorien_titel']}";
                            break;
                        case 4: $str = "{$e['medium_hauptkategorien_titel']} > {$e['medium_unterkategorien_titel']}";
                            break;
                        case 5: $str = count($e['items_physisch_archiv']);
                            break;
                        case 6: $str = count($e['items_digital_archiv']);
                            break;
                        case 7: $str = count($e['items_physisch_extern']);
                            break;
                    }

                    $pdf->SetXY($x, $y);
                    $pdf->MultiCell($metrics[$j]['width'], $row_line_height, iconv('UTF-8', 'windows-1252', $str), $border, $metrics[$j]['align']);
                    $x = $x + $metrics[$j]['width'];
                    $tmp_y_height = $pdf->getY() - $y + 2*$padding_cell;
                    $calc_row_height = ($tmp_y_height > $calc_row_height) ? $tmp_y_height : $calc_row_height;
                }
                
                
                // Draw Table Lines
                $x_rect = $page_padding_left;
                $y_rect = $y - $padding_cell;

                for($j=0;$j < count($metrics);$j++){
                    $pdf->Line(15, $y_rect+$calc_row_height, $page_width-15, $y_rect+$calc_row_height);
                    //$pdf->Rect($x_rect, $y_rect, $metrics[$j]['width'], $calc_row_height, 'D');
                    $x_rect = $x_rect + $metrics[$j]['width'];
                }

                $last_row_height = $calc_row_height;

                if (($y + $last_row_height + 18) > ($page_height - $page_padding_bottom)) $newpage = true;

            }

        }else if ($args['display_type'] == 'detail'){
        
            $page_width = 210.00;
            $page_height = 297.00;

            $page_inner_width = $page_width - $page_padding_left - $page_padding_right;

            $font_size_detail_key = 7;
            $font_size_detail_value = 9;
            $padding_cell = 1;
            $line_height = 5;

            $pdf->SetLineWidth(0.15);
            $pdf->SetDrawColor(230,230,230);

            // Deckblatt
            $pdf->AddPage();
            if ($border) $pdf->Rect($page_padding_left, $page_padding_top, $page_inner_width, $page_height - $page_padding_top - $page_padding_bottom, 'D');

            $x = $page_padding_left;
            $y = $page_padding_top + 50;

            $pdf->SetTextColor(0,0,0);
            $pdf->SetFont('Josefin','B', 35);
            $pdf->SetXY($x, $y);
            $pdf->Cell($page_inner_width, 18, iconv('UTF-8', 'windows-1252', "Findbuch Export"), $border, NULL, 'L');

            $pdf->SetTextColor(100,100,100);
            $pdf->SetFont('JosefinLight','', 12);
            $pdf->SetXY($x, $y+20);
            $pdf->Cell($page_inner_width, 10, iconv('UTF-8', 'windows-1252', "{$archivtitel} | Erstellt: ".date('d.m.Y H:i:s', $date_generiert)), $border, NULL, 'L');
            $pdf->SetXY($x, $y+32);
            $pdf->Cell($page_inner_width, 10, iconv('UTF-8', 'windows-1252', "Detaileinträge | " . count($eintraege) . " Einträge | {totalPages} Seiten"), $border, NULL, 'L');
           
            
            // Detail Single Pages
            $bestand_logo_height = 5.00;
            $bestand_logo_width  = 9.00;

            $page_key_value_padding = 4.00;
            $page_key_value_row_padding = 6.00;
            $page_key_value_width_1 = ($page_inner_width - 11*$page_key_value_padding)/12;
            $page_key_value_width_2 = ($page_inner_width - 5*$page_key_value_padding)/6;
            $page_key_value_width_3 = ($page_inner_width - 3*$page_key_value_padding)/4;
            $page_key_value_width_4 = ($page_inner_width - 2*$page_key_value_padding)/3;
            $page_key_value_width_5 = ($page_inner_width - 1.4*$page_key_value_padding)/2.4;
            $page_key_value_width_6 = ($page_inner_width - 1*$page_key_value_padding)/2;
            
            foreach($eintraege as $e){

                // Page 1 - Header
                $pdf->AddPage();
                if ($border) $pdf->Rect($page_padding_left, $page_padding_top, $page_inner_width, $page_height - $page_padding_top - $page_padding_bottom, 'D');

                $pdf->setFillColor(248, 248, 248);
                $pdf->SetTextColor(0,0,0);

                $header_height = $page_padding_top + $bestand_logo_height + 5;
                $pdf->SetFont('Josefin','B', 16);
                $header_height += $pdf->GetMultiCellHeight($page_inner_width, 10, iconv('UTF-8', 'windows-1252', $e['eintraege_titel']), $border, NULL, 'L');

                //$pdf->Rect(0,0,$page_width, $header_height + 10, 'F');

                $pdf->Image(__DIR__ . "/../../vendor/{$e['bestaende_signatur']}.png", $page_padding_left, $page_padding_top , $bestand_logo_width, $bestand_logo_height);
                $pdf->SetFont('Josefin','', 12);
                $pdf->SetXY($page_padding_left + $bestand_logo_width + 1, $page_padding_top);
                $pdf->Cell(100, $bestand_logo_height, iconv('UTF-8', 'windows-1252', $e['eintraege_id_formatted']), $border, NULL, 'L');

                $pdf->SetFont('Josefin','B', 16);
                $pdf->SetXY($page_padding_left, $page_padding_top + $bestand_logo_height + 10);
                $pdf->MultiCell($page_inner_width, 10, iconv('UTF-8', 'windows-1252', $e['eintraege_titel']), $border, 'L', false);

                $pdf->SetFont('Josefin','', 10);
                $pdf->SetTextColor(100,100,100);
                $pdf->SetXY($page_padding_left, $header_height + 10);
                $pdf->MultiCell($page_inner_width, 4, iconv('UTF-8', 'windows-1252', "Eintrag zuletzt bearbeitet: {$e['datum_bearbeitet_dmy']}"), $border, 'L', false);

                $pdf->SetFont('Josefin','B', 8);
                $pdf->SetTextColor(100,100,100);
                $pdf->SetXY($page_width - $page_padding_right - 50, $page_padding_top);
                $pdf->Cell(50, 4, "VORDERSEITE", $border, NULL, 'R');

                $content_y = $header_height + 30;


                // Page 1 - Content
                $y = $content_y;
                
                $y = $y + $pdf->GOAHeadline($page_padding_left, $y, $page_inner_width, 1, "1. Eintrag");
                $y = $y + $page_key_value_row_padding + $pdf->GOAPrintKeyValue($page_padding_left, $y, $page_inner_width, "Kommentar", $e['eintraege_kommentar']);
                
                $y = $y + $pdf->GOAHeadline($page_padding_left, $y, $page_inner_width, 2, "1.1. Datierung");
                $y = $y + $page_key_value_row_padding + max(
                    array(
                        $pdf->GOAPrintKeyValue($page_padding_left + 0*$page_key_value_padding, $y, $page_key_value_width_4, "Freitext", $e['datierung_text']),
                        $pdf->GOAPrintKeyValue($page_padding_left + 1*$page_key_value_padding + 1*$page_key_value_width_4, $y, $page_key_value_width_4, "Von", $e['datierung_von_dmy']),
                        $pdf->GOAPrintKeyValue($page_padding_left + 2*$page_key_value_padding + 2*$page_key_value_width_4, $y, $page_key_value_width_4, "Bis", $e['datierung_bis_dmy'])
                    )
                );

                $y = $y + $pdf->GOAHeadline($page_padding_left, $y, $page_inner_width, 2, "1.2. Urheber");
                $y = $y + $page_key_value_row_padding + max(
                    array(
                        $pdf->GOAPrintKeyValue($page_padding_left + 0*$page_key_value_padding, $y, $page_key_value_width_3, "Urheber", $e['urheber_name']),
                        $pdf->GOAPrintKeyValue($page_padding_left + 1*$page_key_value_padding+ 1*$page_key_value_width_3, $y, $page_key_value_width_3, "Infos", $e['urheber_infos']),
                        $pdf->GOAPrintKeyValue($page_padding_left + 2*$page_key_value_padding+ 2*$page_key_value_width_3, $y, $page_key_value_width_3, "Verständnis eingeholt", $e['urheber_verstaendnis_eingeholt'] ? "Ja" : "Nein"),
                        $pdf->GOAPrintKeyValue($page_padding_left + 3*$page_key_value_padding+ 3*$page_key_value_width_3, $y, $page_key_value_width_3, "Sperrfrist erloschen", $e['urheber_sperrfrist_erloschen'] ? "Ja" : "Nein")
                    )
                );
                        

                $y = $y + $pdf->GOAHeadline($page_padding_left, $y, $page_key_value_width_3, 2, "1.3. Tags");
                $y = $y + $page_key_value_row_padding + max(
                    array(
                        $pdf->GOAPrintKeyValue($page_padding_left, $y, $page_key_value_width_4, "Hauptobjekte", $e['tags_hauptobjekte'], 'L'),
                        $pdf->GOAPrintKeyValue($page_padding_left + $page_key_value_width_4 + $page_key_value_padding, $y, $page_key_value_width_4, "Nebenobjekte", $e['tags_nebenobjekte'], 'L'),
                        $pdf->GOAPrintKeyValue($page_padding_left + 2*$page_key_value_width_4 + 2*$page_key_value_padding, $y, $page_key_value_width_4, "Personen", $e['tags_personen'], 'L')
                    )
                );

                $y = $y + $pdf->GOAHeadline($page_padding_left, $y, $page_inner_width, 2, "1.4. Thema");
                $y = $y + $page_key_value_row_padding + max(
                    array(
                        $pdf->GOAPrintKeyValue($page_padding_left, $y, $page_key_value_width_6, "Hauptkategorie", $e['thema_hauptkategorien_titel'], 'L'),
                        $pdf->GOAPrintKeyValue($page_padding_left + $page_key_value_width_6 + $page_key_value_padding, $y, $page_key_value_width_6, "Unterkategorie", $e['thema_unterkategorien_titel'], 'L')
                    )
                );

                $y = $y + $pdf->GOAHeadline($page_padding_left, $y, $page_inner_width, 2, "1.5. Medium");
                $y = $y + $page_key_value_row_padding + max(
                    array(
                        $pdf->GOAPrintKeyValue($page_padding_left, $y, $page_key_value_width_6, "Hauptkategorie", $e['medium_hauptkategorien_titel'], 'L'),
                        $pdf->GOAPrintKeyValue($page_padding_left + $page_key_value_width_6 + $page_key_value_padding, $y, $page_key_value_width_6, "Unterkategorie", $e['medium_unterkategorien_titel'], 'L')
                    )
                );

                // Page 1 - Footer
                $pdf->SetTextColor(100,100,100);
                $pdf->SetFont('JosefinLight','I', $font_size_list);

                $field_width = ($page_width - $page_padding_left - $page_padding_right)/2;

                $pdf->SetXY($page_padding_left, $page_height - $page_padding_bottom);
                $pdf->Cell($field_width, $page_footer_height, iconv('UTF-8', 'windows-1252', $archivtitel), $border, NULL, 'L');
                $pdf->SetXY($page_padding_left, $page_height + $page_footer_height - $page_padding_bottom);
                $pdf->Cell($field_width, $page_footer_height, iconv('UTF-8', 'windows-1252', $meta_informationen), $border, NULL, 'L');

                $pdf->SetFillColor(0,0,0);
                $pdf->Code128($page_width - $page_padding_right - 40, $page_height - $page_padding_bottom, "{$e['bestaende_signatur']}{$e['eintraege_id_formatted']}", 40, 10);
                $pdf->Image("https://qrapi.krzn.de/get.php?text=http://archiv.ff-gemeinlebarn.at/?id={$e['bestaende_signatur']}{$e['eintraege_id_formatted']}&ecc_level=Q&size=1&border=0&type=.png", $page_width - $page_padding_right - 55, $page_height - $page_padding_bottom, 10,10);



                // Page 2 - Header
                $pdf->AddPage();
                if ($border) $pdf->Rect($page_padding_left, $page_padding_top, $page_inner_width, $page_height - $page_padding_top - $page_padding_bottom, 'D');

                $pdf->setFillColor(248, 248, 248);
                $pdf->SetTextColor(0,0,0);

                //$pdf->Rect(0,0,$page_width, $header_height + 10, 'F');

                $pdf->Image(__DIR__ . "/../../vendor/{$e['bestaende_signatur']}.png", $page_padding_left, $page_padding_top , $bestand_logo_width, $bestand_logo_height);
                $pdf->SetFont('Josefin','', 12);
                $pdf->SetXY($page_padding_left + $bestand_logo_width + 1, $page_padding_top);
                $pdf->Cell(100, $bestand_logo_height, iconv('UTF-8', 'windows-1252', $e['eintraege_id_formatted']), $border, NULL, 'L');

                $pdf->SetFont('Josefin','B', 8);
                $pdf->SetTextColor(100,100,100);
                $pdf->SetXY($page_width - $page_padding_right - 50, $page_padding_top);
                $pdf->Cell(50, 4, iconv('UTF-8', 'windows-1252', "RÜCKSEITE"), $border, NULL, 'R');

                // Page 2 - Content
                $y = $page_padding_top + $bestand_logo_height + 15;

                $y = $y + $pdf->GOAHeadline($page_padding_left, $y, $page_inner_width, 1, "2. Vorhandene Objekte zum Eintrag");
                $y = $y + $pdf->GOAHeadline($page_padding_left, $y, $page_inner_width, 2, "2.1. Physisch im Archiv");
                foreach($e['items_physisch_archiv'] as $item){
                    $y = $y + $page_key_value_row_padding + max(
                        array(
                            $pdf->GOAPrintKeyValue($page_padding_left + 0*$page_key_value_padding, $y, $page_key_value_width_4, "Behältnis", "{$item['behaeltnisse_bestaende_signatur']}/BEH{$item['behaeltnisse_id_formatted']} ({$item['behaeltnisse_name']})" , 'L'),
                            $pdf->GOAPrintKeyValue($page_padding_left + 1*$page_key_value_padding + 1*$page_key_value_width_4, $y, $page_key_value_width_2, "Quellstück", $item['quellstueck'] ? "Ja"  : "Nein", 'L'),
                            $pdf->GOAPrintKeyValue($page_padding_left + 2*$page_key_value_padding + 1*$page_key_value_width_4 + 1*$page_key_value_width_2, $y, $page_key_value_width_2, "Originalität", $item['originalitaeten_titel'], 'L'),
                            $pdf->GOAPrintKeyValue($page_padding_left + 3*$page_key_value_padding + 1*$page_key_value_width_4 + 2*$page_key_value_width_2, $y, $page_key_value_width_2, "Farbraum", $item['farbraeume_titel'], 'L'),
                            $pdf->GOAPrintKeyValue($page_padding_left + 4*$page_key_value_padding + 1*$page_key_value_width_4 + 3*$page_key_value_width_2, $y, $page_key_value_width_2, "Größe", $item['groesse'], 'L')
                        )
                    );          
                }
                if (count($e['items_physisch_archiv']) == 0){
                    $pdf->SetFont('Josefin','', 9);
                    $pdf->SetXY($page_padding_left, $y);
                    $pdf->Cell(100, 3, iconv('UTF-8', 'windows-1252', "Keine Objekte"), $border, NULL, 'L');
                    $y = $y + $page_key_value_row_padding;
                }

                $pdf->SetXY($page_padding_left, $y);
                $y = $y + $pdf->GOAHeadline($page_padding_left, $y, $page_inner_width, 2, "2.2. Digital im Archiv");
                foreach($e['items_digital_archiv'] as $item){
                    $y = $y + $page_key_value_row_padding + max(
                        array(
                            $pdf->GOAPrintKeyValue($page_padding_left + 0*$page_key_value_padding, $y, $page_key_value_width_1, "Filetype", $item['filetypen_titel'], 'L'),
                            $pdf->GOAPrintKeyValue($page_padding_left + 1*$page_key_value_padding + 1*$page_key_value_width_1, $y, $page_key_value_width_1, "DPI", $item['dpi'], 'L'),
                            $pdf->GOAPrintKeyValue($page_padding_left + 2*$page_key_value_padding + 2*$page_key_value_width_1, $y, $page_key_value_width_2, "Nachbearbeitet", $item['nachbearbeitet'] ? "Ja"  : "Nein", 'L'),
                            $pdf->GOAPrintKeyValue($page_padding_left + 3*$page_key_value_padding + 2*$page_key_value_width_1 + 1*$page_key_value_width_2, $y, $page_key_value_width_2, "Datum", $item['datum_dmy'], 'L'),
                            $pdf->GOAPrintKeyValue($page_padding_left + 4*$page_key_value_padding + 2*$page_key_value_width_1 + 2*$page_key_value_width_2, $y, $page_key_value_width_6, "Beschreibung", $item['beschreibung'], 'L')
                        )
                    );
                }
                if (count($e['items_digital_archiv']) == 0){
                    $pdf->SetFont('Josefin','', 9);
                    $pdf->SetXY($page_padding_left, $y);
                    $pdf->Cell(100, 3, iconv('UTF-8', 'windows-1252', "Keine Objekte"), $border, NULL, 'L');
                    $y = $y + $page_key_value_row_padding;
                }

                $pdf->SetXY($page_padding_left, $y);
                $y = $y + $pdf->GOAHeadline($page_padding_left, $y, $page_inner_width, 2, "2.3. Extern");
                foreach($e['items_physisch_extern'] as $item){
                    $y = $y + $page_key_value_row_padding + max(
                        array(
                            $pdf->GOAPrintKeyValue($page_padding_left + 0*$page_key_value_padding, $y, $page_key_value_width_2, "Quellstück", $item['quellstueck'] ? "Ja"  : "Nein", 'L'),
                            $pdf->GOAPrintKeyValue($page_padding_left + 1*$page_key_value_padding + 1*$page_key_value_width_2, $y, $page_key_value_width_2, "Originalität", $item['originalitaeten_titel'], 'L'),
                            $pdf->GOAPrintKeyValue($page_padding_left + 2*$page_key_value_padding + 2*$page_key_value_width_2, $y, $page_key_value_width_2, "Farbraum", $item['farbraeume_titel'], 'L'),
                            $pdf->GOAPrintKeyValue($page_padding_left + 3*$page_key_value_padding + 3*$page_key_value_width_2, $y, $page_key_value_width_2, "Größe", $item['groesse'], 'L'),
                            $pdf->GOAPrintKeyValue($page_padding_left + 4*$page_key_value_padding + 4*$page_key_value_width_2, $y, $page_key_value_width_4, "Besitzer/Standort", $item['besitzer_extern_name'], 'L')
                        )
                    );
                }
                if (count($e['items_physisch_extern']) == 0){
                    $pdf->SetFont('Josefin','', 9);
                    $pdf->SetXY($page_padding_left, $y);
                    $pdf->Cell(100, 3, iconv('UTF-8', 'windows-1252', "Keine Objekte"), $border, NULL, 'L');
                    $y = $y + $page_key_value_row_padding;
                }

                // Page 2 - Footer
                $pdf->SetTextColor(100,100,100);
                $pdf->SetFont('JosefinLight','I', $font_size_list);

                $field_width = ($page_width - $page_padding_left - $page_padding_right)/2;

                $pdf->SetXY($page_padding_left, $page_height - $page_padding_bottom);
                $pdf->Cell($field_width, $page_footer_height, iconv('UTF-8', 'windows-1252', $archivtitel), $border, NULL, 'L');
                $pdf->SetXY($page_padding_left, $page_height + $page_footer_height - $page_padding_bottom);
                $pdf->Cell($field_width, $page_footer_height, iconv('UTF-8', 'windows-1252', $meta_informationen), $border, NULL, 'L');

                $pdf->SetFillColor(0,0,0);
                $pdf->Code128($page_width - $page_padding_right - 40, $page_height - $page_padding_bottom, "{$e['bestaende_signatur']}{$e['eintraege_id_formatted']}", 40, 10);
                $pdf->Image("https://qrapi.krzn.de/get.php?text=http://archiv.ff-gemeinlebarn.at/?id={$e['bestaende_signatur']}{$e['eintraege_id_formatted']}&ecc_level=Q&size=1&border=0&type=.png", $page_width - $page_padding_right - 55, $page_height - $page_padding_bottom, 10,10);


            }
        }

        $gen = date('Ymd_His', $date_generiert);
        $pdf->Output("Findbuch_{$args['display_type']}_{$gen}.pdf", "I");
        
        return $this->response->withHeader('Content-type', 'application/pdf');
    });

?>