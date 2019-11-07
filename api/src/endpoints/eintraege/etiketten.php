<?php

    $app->get('/v1/etiketten/{type}', function ($request, $response, $args) {

        $etiketten_version = "1.5";
        $type_eintr_kat = '';
        $ids = array();
        $eintraege = array();
        $kategorien = array();
        $medien = array();
        error_reporting(0);
        $format = $request->getParam('format');

        if ($format == 'a4-2x6-qr-text'){ // => Etiketten für Trennstreifen
            $datum_letztes_etiketten_generieren = 'datum_letztes_etiketten_generieren_gross_qr_text';
            $type_eintr_kat = 'eintraege';

        } else if ($format == 'a4-2x4-qr-text'){ // => Schilder Sichttaschen
            $datum_letztes_etiketten_generieren = 'datum_letztes_etiketten_generieren_gross_qr_text';
            $type_eintr_kat = 'eintraege';

        } else if ($format == 'a4-4x10-qr-text'){ // => Etiketten für direkt
            $datum_letztes_etiketten_generieren = 'datum_letztes_etiketten_generieren_klein_qr_text';
            $type_eintr_kat = 'eintraege';

        } else if ($format == 'a4-4x10-barcode'){ // => Barcodes für Trennstreifen
            $datum_letztes_etiketten_generieren = 'datum_letztes_etiketten_generieren_klein_barcode';
            $type_eintr_kat = 'eintraege';

        } else if ($format == 'a4-2x2-themen-kategorien'){ // => Etiketten Kategorien
            $type_eintr_kat = 'kategorien';

        } else if ($format == 'a4-2x2-medien-kategorien'){ // => Etiketten Kategorien
            $type_eintr_kat = 'medien';

        }

        if ($args['type'] == 'ids'){

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

                if ($type_eintr_kat == 'eintraege'){
        
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
                            thema_hauptkategorien.titel AS thema_hauptkategorien_titel
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

                    if ($request->getParam('bearbeitet_timestamp') == 1){
            
                        $sth = $this->db->prepare(
                            "UPDATE 
                                eintraege
                            SET
                                eintraege.$datum_letztes_etiketten_generieren = CURRENT_TIMESTAMP 
                            WHERE 
                                eintraege.id IN {$id_arr_str} AND
                                eintraege.bestaende_id = :bestaende_id"
                        );
                        $sth->bindParam(':bestaende_id', $bestaende_id, PDO::PARAM_INT);
                
                        $sth->execute();
                    }
                    
                } elseif ($type_eintr_kat == 'kategorien') {

                    $sth = $this->db->prepare(
                        "SELECT 
                            thema_unterkategorien.id AS thema_unterkategorien_id,
                            thema_hauptkategorien.id AS thema_hauptkategorien_id,
                            bestaende.id AS bestaende_id,
                            bestaende.signatur AS bestaende_signatur,
                            thema_unterkategorien.id AS thema_unterkategorien_id,
                            thema_unterkategorien.titel AS thema_unterkategorien_titel,
                            thema_hauptkategorien.id AS thema_hauptkategorien_id,
                            thema_hauptkategorien.titel AS thema_hauptkategorien_titel
                        FROM 
                        thema_unterkategorien 
                        LEFT JOIN
                            thema_hauptkategorien ON thema_hauptkategorien.id = thema_unterkategorien.thema_hauptkategorien_id
                        LEFT JOIN 
                            bestaende ON bestaende.id = thema_hauptkategorien.bestaende_id
                        WHERE 
                            thema_hauptkategorien.bestaende_id = :bestaende_id AND
                            thema_unterkategorien.id IN {$id_arr_str}
                        ORDER BY 
                            thema_unterkategorien.id"
                    
                    );

                    $sth->bindParam(':bestaende_id', $bestaende_id, PDO::PARAM_INT);
                    $sth->execute();

                    $kategorien = array_merge($kategorien, $sth->fetchAll());

                } elseif ($type_eintr_kat == 'medien') {

                    $sth = $this->db->prepare(
                        "SELECT 
                            medium_unterkategorien.id AS medium_unterkategorien_id,
                            medium_unterkategorien.titel AS medium_unterkategorien_titel,
                            medium_hauptkategorien.id AS medium_hauptkategorien_id,
                            medium_hauptkategorien.titel AS medium_hauptkategorien_titel
                        FROM 
                            medium_unterkategorien 
                        LEFT JOIN
                            medium_hauptkategorien ON medium_hauptkategorien.id = medium_unterkategorien.medium_hauptkategorien_id
                        WHERE 
                            medium_unterkategorien.id IN {$id_arr_str}
                        ORDER BY 
                            medium_unterkategorien.id"
                    
                    );

                    $sth->execute();

                    $medien = array_merge($medien, $sth->fetchAll());
                }
            }

        }else if ($args['type'] == 'bearbeitet-gedruckt' || $args['type'] == 'physische-items-archiv'){

            if ($type_eintr_kat == 'eintraege'){

                $physische_items_arcwhiv = ($args['type'] == 'physische-items-archiv') ? 'OR 1' : '';

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
                        eintraege.$datum_letztes_etiketten_generieren 
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
                        (
                            (eintraege.$datum_letztes_etiketten_generieren IS NULL) OR
                            (eintraege.$datum_letztes_etiketten_generieren < eintraege.datum_bearbeitet) 
                            $physische_items_archiv
                        ) AND
                        (
                            SELECT 
                                COUNT(*) 
                            FROM 
                                items_physisch 
                            WHERE 
                                items_physisch.eintraege_id = eintraege.id AND 
                                items_physisch.eintraege_bestaende_id = bestaende.id AND
                                besitzer_goa = 1
                        ) > 0
                    ORDER BY 
                        bestaende.id, 
                        eintraege.id"
                );
        
                $sth->execute();
                $eintraege = $sth->fetchAll();

                if ($request->getParam('bearbeitet_timestamp') == 1){

                    foreach($eintraege as $e){
                        $sth = $this->db->prepare(
                            "UPDATE 
                                eintraege
                            SET
                                eintraege.$datum_letztes_etiketten_generieren = CURRENT_TIMESTAMP 
                            WHERE 
                                eintraege.id  = :id AND eintraege.bestaende_id = :bestaende_id"
                        );
                        $sth->bindParam(':id', $e['eintraege_id'], PDO::PARAM_INT);
                        $sth->bindParam(':bestaende_id', $e['bestaende_id'], PDO::PARAM_INT);
                
                        $sth->execute();
                    }
                }
            }
        }

        require_once(__DIR__ . '/../../vendor/GOA_PDF.php');

        $pdf = new GOA_PDF('P','mm','A4');

        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        $pdf->AddFont('JosefinLight','','JosefinSans-Light.php');
        $pdf->AddFont('JosefinLight','I','JosefinSans-LightItalic.php');
        $pdf->AddFont('Josefin','','JosefinSans-Regular.php');
        $pdf->AddFont('JosefinSemiBold','B','JosefinSans-SemiBold.php');
        $pdf->AddFont('Josefin','B','JosefinSans-Bold.php');

        $row = 0;
        $column = 0;
        $border = 0;

        $padding_kategorien = 11;
        $padding_gross = 4;
        $padding_klein = 2;

        $archivtitel = "Ortsarchiv Gemeinlebarn";
        $meta_informationen = "Etikett Version: {$etiketten_version}, Etikett generiert: ". date('d.m.Y H:i:s');

        // Einträge
        if ($type_eintr_kat == 'eintraege'){

            if ($format == 'a4-2x6-qr-text'){

                $start_x = 10.4;
                $start_y = 23;

                $column_width = 94.5;
                $row_height = 41.7;
                $column_max = 2;
                $row_max = 6;

            } else if ($format == 'a4-2x4-qr-text'){

                $start_x = 5;
                $start_y = 20;

                $column_width = 99.5;
                $row_height = 60;
                $column_max = 2;
                $row_max = 4;

            } else if ($format == 'a4-4x10-qr-text' || $format == 'a4-4x10-barcode'){

                $start_x = 10.4;
                $start_y = 23;

                $column_width = 47.3;
                $row_height = 24.9;
                $column_max = 4;
                $row_max = 10;
            }

            for($i=0;$i < $request->getParam('range_leer'); $i++){

                if ($column == $column_max && $row == $row_max){
                    $pdf->AddPage();
                    $row = 1;
                    $column = 1;
                }else{
                    if($column % $column_max){
                        $column++;
                    }else{
                        $row++;
                        $column = 1;
                    }
                }

                $x = $start_x + (($column - 1) * $column_width);
                $y = $start_y + (($row - 1) * $row_height);
            }

            foreach($eintraege as $e){

                if ($column == $column_max && $row == $row_max){
                    $pdf->AddPage();
                    $row = 1;
                    $column = 1;
                }else{
                    if($column % $column_max){
                        $column++;
                    }else{
                        $row++;
                        $column = 1;
                    }
                }

                $datierung = ($e['datierung_text'] == "") ? "Unbekannt" : $e['datierung_text'];

                $x = $start_x + (($column - 1) * $column_width);
                $y = $start_y + (($row - 1) * $row_height);

                if ($format == 'a4-2x6-qr-text'){

                    if ($border){
                        $pdf->SetXY($x, $y);
                        $pdf->Cell($column_width, $row_height, "", $border);
                        $pdf->SetXY($x + $padding_gross, $y + $padding_gross);
                        $pdf->Cell($column_width - 2*$padding_gross, $row_height - 2*$padding_gross, "", $border);
                    }

                    //$pdf->setFillColor(0, 0, 0);
                    //$pdf->Code128($x + $column_width - 57, $y + 4, "{$e['bestaende_signatur']}{$e['eintraege_id_formatted']}", 42, 5);

                    $pdf->Image(__DIR__ . "/../../vendor/{$e['bestaende_signatur']}.png", $x + $column_width - 13, $y + 4, 9, 5);
                    $pdf->Image("https://qrapi.krzn.de/get.php?text=http://archiv.ff-gemeinlebarn.at/?id={$e['bestaende_signatur']}{$e['eintraege_id_formatted']}&ecc_level=Q&size=1&border=0&type=.png", $x + $column_width - 18, $y + 12, 14, 14);
                    
                    $pdf->setFillColor(245, 245, 245);
                    $pdf->Rect($x-1, $y + $row_height - 13, $column_width + 2, 10, 'F');

                    $pdf->SetTextColor(30,30,30);
                    $pdf->SetFont('JosefinLight','I', 7);
                    $pdf->SetXY($x + 3, $y + 4);
                    $pdf->Cell($column_width - 53, 2, iconv('UTF-8', 'windows-1252', $archivtitel), $border);

                    $pdf->SetTextColor(0,0,0);
                    $pdf->SetFont('JosefinSemiBold','B', 15);
                    $pdf->SetXY($x + 3, $y + 8);
                    $pdf->Cell($column_width - 53, 5, iconv('UTF-8', 'windows-1252', "{$e['bestaende_signatur']}{$e['eintraege_id_formatted']}"), $border);

                    $pdf->SetTextColor(0,0,0);
                    $pdf->SetFont('Josefin','', 10);
                    $pdf->SetXY($x + 3, $y + 16);
                    $pdf->drawTextBox(iconv('UTF-8', 'windows-1252', $e['eintraege_titel']), $column_width - 24, 11, 'L', 'T', $border);

                    $pdf->SetTextColor(0,0,0);

                    $pdf->SetFont('JosefinSemiBold','B', 5.5);
                    $pdf->SetXY($x + 3, $y + $row_height - 12);
                    $pdf->Cell(10, 2, iconv('UTF-8', 'windows-1252', "Datierung:"), $border);
                    $pdf->SetFont('JosefinLight','', 5.5);
                    $pdf->SetXY($x + 14, $y + $row_height - 12);
                    $pdf->Cell($column_width - 17, 2, iconv('UTF-8', 'windows-1252', $datierung), $border);

                    $pdf->SetFont('JosefinSemiBold','B', 5.5);
                    $pdf->SetXY($x + 3, $y + $row_height - 9); 
                    $pdf->Cell(10, 2, iconv('UTF-8', 'windows-1252', "Medium:"), $border);
                    $pdf->SetFont('JosefinLight','', 5.5);
                    $pdf->SetXY($x + 14, $y + $row_height - 9);
                    $pdf->Cell($column_width - 17, 2, iconv('UTF-8', 'windows-1252', "{$e['medium_hauptkategorien_titel']} / {$e['medium_unterkategorien_titel']}"), $border);
                    
                    $pdf->SetFont('JosefinSemiBold','B', 5.5);
                    $pdf->SetXY($x + 3, $y + $row_height - 6); 
                    $pdf->Cell(10, 2, iconv('UTF-8', 'windows-1252', "Thema:"), $border);
                    $pdf->SetFont('JosefinLight','', 5.5);
                    $pdf->SetXY($x + 14, $y + $row_height - 6);
                    $pdf->Cell($column_width - 17, 2, iconv('UTF-8', 'windows-1252', "{$e['thema_hauptkategorien_titel']} / {$e['thema_unterkategorien_titel']}"), $border); //iconv('UTF-8', 'windows-1252', $e['thema_unterkategorien_titel'])
                
                    // Meta Data
                    $pdf->SetTextColor(100,100,100);
                    $pdf->SetFont('Josefin','', 3);
                    $pdf->SetXY($x, $y + $row_height - 2);
                    $pdf->Cell($column_width, 1, iconv('UTF-8', 'windows-1252', $meta_informationen), $border, 0, 'R');

                } else if ($format == 'a4-2x4-qr-text'){

                    $padding = 8;

                    if ($border){
                        $pdf->SetXY($x, $y);
                        $pdf->Cell($column_width, $row_height, "", $border);
                        $pdf->SetXY($x + $padding, $y + $padding);
                        $pdf->Cell($column_width - 2*$padding, $row_height - 2*$padding, "", $border);
                    }

                    $pdf->Image(__DIR__ . "/../../vendor/{$e['bestaende_signatur']}.png", $x + $column_width - $padding - 9, $y + $padding, 9, 5);
                    $pdf->Image("https://qrapi.krzn.de/get.php?text=http://archiv.ff-gemeinlebarn.at/?id={$e['bestaende_signatur']}{$e['eintraege_id_formatted']}&ecc_level=Q&size=1&border=0&type=.png", $x + $column_width - $padding - 14, $y + $padding + 8, 14, 14);
                    
                    $pdf->setFillColor(245, 245, 245);
                    $pdf->Rect($x-1, $y + $row_height - $padding - 11, $column_width + 2, 14, 'F');

                    $pdf->SetTextColor(30,30,30);
                    $pdf->SetFont('JosefinLight','I', 7);
                    $pdf->SetXY($x + $padding - 1, $y + $padding);
                    $pdf->Cell($column_width - 53, 2, iconv('UTF-8', 'windows-1252', $archivtitel), $border);

                    $pdf->SetTextColor(0,0,0);
                    $pdf->SetFont('JosefinSemiBold','B', 15);
                    $pdf->SetXY($x + $padding - 1, $y + $padding + 4);
                    $pdf->Cell($column_width - $padding - 49, 5, iconv('UTF-8', 'windows-1252', "{$e['bestaende_signatur']}{$e['eintraege_id_formatted']}"), $border);

                    $pdf->SetTextColor(0,0,0);
                    $pdf->SetFont('Josefin','', 10);
                    $pdf->SetXY($x + $padding - 1, $y + $padding + 12);
                    $pdf->drawTextBox(iconv('UTF-8', 'windows-1252', $e['eintraege_titel']), $column_width - $padding - 28, 20, 'L', 'T', $border);

                    $pdf->SetTextColor(0,0,0);

                    $pdf->SetFont('JosefinSemiBold','B', 5.5);
                    $pdf->SetXY($x + $padding - 1, $y + $row_height - $padding - 8);
                    $pdf->Cell(10, 2, iconv('UTF-8', 'windows-1252', "Datierung:"), $border);
                    $pdf->SetFont('JosefinLight','', 5.5);
                    $pdf->SetXY($x + $padding + 10, $y + $row_height - $padding - 8);
                    $pdf->Cell($column_width - 2*$padding - 10, 2, iconv('UTF-8', 'windows-1252', $datierung), $border);

                    $pdf->SetFont('JosefinSemiBold','B', 5.5);
                    $pdf->SetXY($x + $padding - 1, $y + $row_height - $padding - 5); 
                    $pdf->Cell(10, 2, iconv('UTF-8', 'windows-1252', "Medium:"), $border);
                    $pdf->SetFont('JosefinLight','', 5.5);
                    $pdf->SetXY($x + $padding + 10, $y + $row_height - $padding - 5);
                    $pdf->Cell($column_width - 2*$padding - 10, 2, iconv('UTF-8', 'windows-1252', "{$e['medium_hauptkategorien_titel']} / {$e['medium_unterkategorien_titel']}"), $border);
                    
                    $pdf->SetFont('JosefinSemiBold','B', 5.5);
                    $pdf->SetXY($x + $padding - 1, $y + $row_height - $padding - 2); 
                    $pdf->Cell(10, 2, iconv('UTF-8', 'windows-1252', "Thema:"), $border);
                    $pdf->SetFont('JosefinLight','', 5.5);
                    $pdf->SetXY($x + $padding + 10, $y + $row_height - $padding - 2);
                    $pdf->Cell($column_width - 2*$padding - 10, 2, iconv('UTF-8', 'windows-1252', "{$e['thema_hauptkategorien_titel']} / {$e['thema_unterkategorien_titel']}"), $border); //iconv('UTF-8', 'windows-1252', $e['thema_unterkategorien_titel'])
                
                    // Meta Data
                    $pdf->SetTextColor(100,100,100);
                    $pdf->SetFont('Josefin','', 3);
                    $pdf->SetXY($x +1, $y + $row_height - 3);
                    $pdf->Cell($column_width - 2, 1, iconv('UTF-8', 'windows-1252', $meta_informationen), $border, 0, 'R');


                    // Schnitt Rahmen
                    $pdf->SetLineWidth(0.08);
                    $pdf->SetDrawColor(220,220,220);
                    $pdf->SetXY($x, $y);
                    $pdf->Cell($column_width, $row_height, "", 1);

                } else if ($format == 'a4-4x10-qr-text'){

                    $qr_size = 12;

                    if ($border){
                        $pdf->SetXY($x, $y);
                        $pdf->Cell($column_width, $row_height, "", $border);
                        $pdf->SetXY($x + $padding_klein, $y + $padding_klein);
                        $pdf->Cell($column_width - 2*$padding_klein, $row_height - 2*$padding_klein, "", $border);
                    }

                    $pdf->Image(__DIR__ . "/../../vendor/{$e['bestaende_signatur']}.png", $x + $column_width - 9 - $padding_klein, $y + $padding_klein, 9, 5);

                    $pdf->SetTextColor(30,30,30);
                    $pdf->SetFont('JosefinLight','I', 5);
                    $pdf->SetXY($x + $padding_klein - 1, $y + $padding_klein);
                    $pdf->Cell(30, 2, iconv('UTF-8', 'windows-1252', $archivtitel), $border);
                    
                    $pdf->SetTextColor(0,0,0);
                    $pdf->SetFont('JosefinSemiBold','B', 13);
                    $pdf->SetXY($x + $padding_klein - 1, $y + $padding_klein + 3);
                    $pdf->Cell(30, 5, iconv('UTF-8', 'windows-1252', "{$e['bestaende_signatur']}{$e['eintraege_id_formatted']}"), $border);

                    $pdf->Image("https://qrapi.krzn.de/get.php?text=http://archiv.ff-gemeinlebarn.at/?id={$e['bestaende_signatur']}{$e['eintraege_id_formatted']}&ecc_level=Q&size=1&border=0&type=.png", $x + $column_width - $qr_size - $padding_klein, $y + $row_height - $qr_size - $padding_klein - 2, $qr_size, $qr_size);
                    
                    // Meta Data
                    $pdf->SetTextColor(100,100,100);
                    $pdf->SetFont('Josefin','', 3);
                    $pdf->SetXY($x + $padding_klein - 1, $y + $row_height - $padding_klein);
                    $pdf->Cell($column_width - ((2 * $padding_klein) - 2), 1, iconv('UTF-8', 'windows-1252', $meta_informationen), $border, 0, 'R');



                    
                } else if ($format == 'a4-4x10-barcode'){

                    if ($border){
                        $pdf->SetXY($x, $y);
                        $pdf->Cell($column_width, $row_height, "", $border);
                        $pdf->SetXY($x + $padding_klein, $y + $padding_klein);
                        $pdf->Cell($column_width - 2*$padding_klein, $row_height - 2*$padding_klein, "", $border);
                    }

                    $pdf->Image(__DIR__ . "/../../vendor/{$e['bestaende_signatur']}.png", $x + $column_width - 9 - $padding_klein, $y + $padding_klein, 9, 5);
                    
                    $pdf->SetTextColor(30,30,30);
                    $pdf->SetFont('JosefinLight','I', 5);
                    $pdf->SetXY($x + $padding_klein - 1, $y + $padding_klein);
                    $pdf->Cell(30, 2, iconv('UTF-8', 'windows-1252', $archivtitel), $border);
                    
                    $pdf->SetTextColor(0,0,0);
                    $pdf->SetFont('JosefinSemiBold','B', 13);
                    $pdf->SetXY($x + $padding_klein - 1, $y + $padding_klein + 3);
                    $pdf->Cell(30, 5, iconv('UTF-8', 'windows-1252', "{$e['bestaende_signatur']}{$e['eintraege_id_formatted']}"), $border);

                    $pdf->setFillColor(0, 0, 0);
                    $pdf->Code128($x + $padding_klein, $y + 11, "{$e['bestaende_signatur']}{$e['eintraege_id_formatted']}", $column_width - $padding_klein * 2, $row_height - 15);
                    
                    // Meta Data
                    $pdf->SetTextColor(100,100,100);
                    $pdf->SetFont('Josefin','', 3);
                    $pdf->SetXY($x + $padding_klein - 1, $y + $row_height - $padding_klein);
                    $pdf->Cell($column_width - ((2 * $padding_klein) - 2), 1, iconv('UTF-8', 'windows-1252', $meta_informationen), $border, 0, 'R');
                }
                
            }
            
            // Kategorien
        } elseif ($type_eintr_kat == 'kategorien') {
    
            if ($format == 'a4-2x2-themen-kategorien'){

                $start_x = 0;
                $start_y = 0;

                $column_width = 105;
                $row_height = 148.5;
                $column_max = 2;
                $row_max = 2;

            }

            for($i=0;$i < $request->getParam('range_leer'); $i++){

                if ($column == $column_max && $row == $row_max){
                    $pdf->AddPage();
                    $row = 1;
                    $column = 1;
                }else{
                    if($column % $column_max){
                        $column++;
                    }else{
                        $row++;
                        $column = 1;
                    }
                }

                $x = $start_x + (($column - 1) * $column_width);
                $y = $start_y + (($row - 1) * $row_height);
            }

            foreach($kategorien as $k){
            
                if ($column == $column_max && $row == $row_max){
                    $pdf->AddPage();
                    $row = 1;
                    $column = 1;
                }else{
                    if($column % $column_max){
                        $column++;
                    }else{
                        $row++;
                        $column = 1;
                    }
                }

                $x = $start_x + (($column - 1) * $column_width);
                $y = $start_y + (($row - 1) * $row_height);

                if ($format == 'a4-2x2-themen-kategorien'){

                    if ($border){
                        $pdf->SetXY($x, $y);
                        $pdf->Cell($column_width, $row_height, "", $border);
                        $pdf->SetXY($x + $padding_kategorien, $y + $padding_kategorien);
                        $pdf->Cell($column_width - 2*$padding_kategorien, $row_height - 2*$padding_kategorien, "", $border);
                    }

                    $pdf->Image(__DIR__ . "/../../vendor/{$k['bestaende_signatur']}.png", $x + $padding_kategorien, $y + $padding_kategorien + 5, 14, 7.77);
                    
                    $pdf->SetTextColor(30,30,30);
                    $pdf->SetFont('JosefinLight','I', 8);
                    $pdf->SetXY($x + $padding_kategorien - 1, $y + $padding_kategorien - 1);
                    $pdf->Cell(30, 2, iconv('UTF-8', 'windows-1252', $archivtitel), $border);

                    // Hauptkategorie
                    $pdf->SetTextColor(128, 128, 128);
                    $pdf->SetFont('Josefin','', 12);
                    $pdf->SetXY($x + $padding_kategorien - 1, $y + $padding_kategorien + 27);
                    $pdf->Write(3, "Hauptkategorie");

                    $pdf->SetTextColor(191, 191, 191);
                    $pdf->SetFont('Josefin','', 6);
                    $pdf->Write(2, "  {$k['thema_hauptkategorien_id']}");

                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetFont('Josefin','', 16);
                    $pdf->SetXY($x + $padding_kategorien - 1, $y + $padding_kategorien + 36);
                    $pdf->drawTextBox(iconv('UTF-8', 'windows-1252', $k['thema_hauptkategorien_titel']), $column_width - 2*$padding_kategorien + 1, 30, 'L', 'T', $border);

                    // Unterkategorie
                    $pdf->SetTextColor(128, 128, 128);
                    $pdf->SetFont('Josefin','', 12);
                    $pdf->SetXY($x + $padding_kategorien - 1, $y + $padding_kategorien + 77);
                    $pdf->Write(3, "Unterkategorie");

                    $pdf->SetTextColor(191, 191, 191);
                    $pdf->SetFont('Josefin','', 6);
                    $pdf->Write(2, "  {$k['thema_unterkategorien_id']}");

                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetFont('Josefin','', 22);
                    $pdf->SetXY($x + $padding_kategorien - 1, $y + $padding_kategorien + 85);
                    $pdf->drawTextBox(iconv('UTF-8', 'windows-1252', $k['thema_unterkategorien_titel']), $column_width - 2*$padding_kategorien + 1, 40, 'L', 'T', $border);

                }
                
            }
            
        // Medien
        } elseif ($type_eintr_kat == 'medien') {

            if ($format == 'a4-2x2-medien-kategorien'){

                $start_x = 0;
                $start_y = 0;

                $column_width = 105;
                $row_height = 148.5;
                $column_max = 2;
                $row_max = 2;

            }

            for($i=0;$i < $request->getParam('range_leer'); $i++){

                if ($column == $column_max && $row == $row_max){
                    $pdf->AddPage();
                    $row = 1;
                    $column = 1;
                }else{
                    if($column % $column_max){
                        $column++;
                    }else{
                        $row++;
                        $column = 1;
                    }
                }

                $x = $start_x + (($column - 1) * $column_width);
                $y = $start_y + (($row - 1) * $row_height);
            }

            foreach($medien as $m){
            
                if ($column == $column_max && $row == $row_max){
                    $pdf->AddPage();
                    $row = 1;
                    $column = 1;
                }else{
                    if($column % $column_max){
                        $column++;
                    }else{
                        $row++;
                        $column = 1;
                    }
                }

                $x = $start_x + (($column - 1) * $column_width);
                $y = $start_y + (($row - 1) * $row_height);

                if ($format == 'a4-2x2-medien-kategorien'){

                    if ($border){
                        $pdf->SetXY($x, $y);
                        $pdf->Cell($column_width, $row_height, "", $border);
                        $pdf->SetXY($x + $padding_kategorien, $y + $padding_kategorien);
                        $pdf->Cell($column_width - 2*$padding_kategorien, $row_height - 2*$padding_kategorien, "", $border);
                    }

                    $pdf->Image(__DIR__ . "/../../vendor/GOA.png", $x + $padding_kategorien, $y + $padding_kategorien + 5, 14, 7.77);
                    $pdf->Image(__DIR__ . "/../../vendor/GFA.png", $x + $padding_kategorien + 1*16, $y + $padding_kategorien + 5, 14, 7.77);
                    $pdf->Image(__DIR__ . "/../../vendor/GSA.png", $x + $padding_kategorien + 2*16, $y + $padding_kategorien + 5, 14, 7.77);
                    
                    $pdf->SetTextColor(30,30,30);
                    $pdf->SetFont('JosefinLight','I', 8);
                    $pdf->SetXY($x + $padding_kategorien - 1, $y + $padding_kategorien - 1);
                    $pdf->Cell(30, 2, iconv('UTF-8', 'windows-1252', $archivtitel), $border);

                    // Hauptkategorie
                    $pdf->SetTextColor(128, 128, 128);
                    $pdf->SetFont('Josefin','', 12);
                    $pdf->SetXY($x + $padding_kategorien - 1, $y + $padding_kategorien + 27);
                    $pdf->Write(3, "Hauptkategorie");

                    $pdf->SetTextColor(191, 191, 191);
                    $pdf->SetFont('Josefin','', 6);
                    $pdf->Write(2, "  {$m['medium_hauptkategorien_id']}");

                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetFont('Josefin','', 16);
                    $pdf->SetXY($x + $padding_kategorien - 1, $y + $padding_kategorien + 36);
                    $pdf->drawTextBox(iconv('UTF-8', 'windows-1252', $m['medium_hauptkategorien_titel']), $column_width - 2*$padding_kategorien + 1, 30, 'L', 'T', $border);

                    // Unterkategorie
                    $pdf->SetTextColor(128, 128, 128);
                    $pdf->SetFont('Josefin','', 12);
                    $pdf->SetXY($x + $padding_kategorien - 1, $y + $padding_kategorien + 77);
                    $pdf->Write(3, "Unterkategorie");

                    $pdf->SetTextColor(191, 191, 191);
                    $pdf->SetFont('Josefin','', 6);
                    $pdf->Write(2, "  {$m['medium_unterkategorien_id']}");

                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetFont('Josefin','', 22);
                    $pdf->SetXY($x + $padding_kategorien - 1, $y + $padding_kategorien + 85);
                    $pdf->drawTextBox(iconv('UTF-8', 'windows-1252', $m['medium_unterkategorien_titel']), $column_width - 2*$padding_kategorien + 1, 40, 'L', 'T', $border);

                }
                
            }
        }
    
        $pdf->Output("Etiketten.pdf", "I");
        
        return $this->response->withHeader('Content-type', 'application/pdf');
    });

?>