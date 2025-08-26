<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Cartes d'Étudiant - EPUMA</title>
    <style>
        /* 📜 On commande la page pour un placement parfait sur la feuille A4 */
        @page {
            margin: 10mm; /* Marge de la feuille pour la découpe */
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background-color: #fff;
            margin: 0;
            font-size: 8pt; /* Taille de base pour la carte */
        }

        /* 🖨️ La planche d'impression. Une table maîtresse pour 2 colonnes. */
        .print-sheet-table {
            width: 100%;
            border-spacing: 5mm; /* Espace entre les cartes */
            border-collapse: separate;
        }

        .print-sheet-table > tbody > tr > td {
            vertical-align: top;
            width: 50%;
        }

        /** 🃏 La carte. Dimensions fixes en points (pt), l'unité préférée des PDF. */
        /* 1mm ≈ 2.83pt. CR80 = 85.6mm x 54mm */
        .student-card {
            width: 243pt;
            height: 153pt;
            background: white;
            border: 1px dashed #999; /* Guide de découpe */
            overflow: hidden; 
            position: relative; /* Contexte pour le positionnement des éléments internes */
        }

        /** ============== RECTO ============== **/
        .recto-header {
            background-color: #003366;
            color: white;
            text-align: center;
            font-size: 6pt;
            font-weight: bold;
            padding: 2mm;
            border-bottom: 2px solid #3b5998;
        }

        .recto-body-table {
            width: 100%;
            height: calc(153pt - 10mm); /* Hauteur de la carte moins le header */
        }
        
        .photo-cell {
            width: 35%;
            vertical-align: top;
            text-align: center;
            padding: 3mm 1mm 3mm 3mm;
        }
        .student-photo {
            width: 70pt;
            height: 80pt;
            object-fit: cover; /* Pour garder les proportions de la photo */
            border: 1px solid #3b5998;
            margin-bottom: 2mm;
        }
        .matricule-box {
            background: white;
            text-align: center;
            font-size: 6pt;
            padding: 1px;
            border: 1px solid #fff;
            margin: -5mm auto 0 auto; /* Remonte sur la photo */
            position: relative; /* Pour être au-dessus du flux */
            width: 80%;
        }
        .matricule-box-label { display: block; font-weight: bold; color: #981e32; }

        .signature-stamp { width: 90%; margin-top: 5pt; }
        .rector-name { font-size: 6pt; font-weight: bold; margin: 0; padding: 0; }

        .info-cell {
            width: 65%;
            vertical-align: top;
            padding: 3mm 3mm 3mm 4mm;
            position: relative;
        }
        .card-title {
            color: #981e32;
            font-family: 'Helvetica', 'Arial Black', sans-serif;
            font-size: 8pt;
            text-align: left;
            margin-bottom: 3mm;
            letter-spacing: 1px;
        }
        .logo-unstim {
            width: 10mm;
            position: absolute;
            top: 1.5mm;
            right: 1.5mm;
        }
        .info-field {
            text-align: left;
            margin-bottom: 2mm;
            font-size: 6pt;
        }
        .info-field-label { display: block; color: #555; font-size: 5pt; }
        .info-field-value { font-weight: bold; color: #333; }
        
        .qr-code { width: 10mm; }

        .year-banner {
            background-color: #3b5998;
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 6pt;
            padding: 1mm 6mm;
            position: absolute;
            bottom: 0;
            left: 0%; 
            /* right: 0; */
        }

        /** ============== VERSO ============== **/
        .verso { margin-top: 15mm; }
        .magnetic-stripe {
            background: #3d3d3d;
            height: 10mm;
            width: 100%;
            position: absolute;
            top: 3mm;
            left: 0;
        }
        .verso-content {
            padding: 3mm;
            position: relative;
            top: 15mm;
            left: 0;
            right: 0;
            bottom: 0;
            font-size: 6pt;
            color: #333;
        }
        .signature-panel { background: #e0e0e0; height: 10mm; }
        .signature-panel-label { font-size: 7pt; color: #555; padding-left: 2mm; }
        .card-info-text { margin-top: 1mm; line-height: 1.4; }
        .card-info-text hr { border: 0; border-top: 1px solid #ccc; margin: 1mm 0; }
        .logo-verso { width: 8mm; height: 8mm; position: absolute; bottom: 17mm; right: 3mm; opacity: 0.8; }
    </style>
</head>
<body>

    <!-- 🖨️ La planche d'impression. Prête à être peuplée par une boucle Blade. -->
    <table class="print-sheet-table">
        <tr>

            <!-- === CARTE 1 === -->
            <td>
                <!-- RECTO -->
                <div class="student-card recto">
                    <div class="recto-header">
                        ECOLE POLYTECHNIQUE UNIVERSITAIRE DES METIERS D'AVENIR (EPUMA) LE PHENIX
                    </div>

                    <table class="recto-body-table">
                        <tr>
                            <td class="photo-cell">
                                <div class="matricule-box">
                                    <span class="matricule-box-label">MATRICULE</span>
                                    <span>{{ $user->matricule }}</span>
                                </div>
                                @if(isset($user->profile) && $user->profile && file_exists(public_path('storage/' . $user->profile)))
                                    <img src="{{ public_path('storage/' . $user->profile) }}" alt="Photo de l'étudiant" class="student-photo" />
                                @else
                                    <img src="{{ public_path('storage/user_placeholder.jpg') }}" alt="Photo de l'étudiant" class="student-photo" />
                                @endif

                            </td>
                            <td class="info-cell">
                                <img src="{{ public_path('storage/logo-epuma.png') }}" alt="Logo" class="logo-unstim" />
                                <div class="card-title">CARTE D'ETUDIANT</div>

                                <!-- ✨ MODIFICATION CHIRURGICALE ✨ -->
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="width: 50%; padding: 0; vertical-align: top;">
                                            <div class="info-field">
                                                <span class="info-field-label">Nom:</span>
                                                <span class="info-field-value">{{ $user->lastname }}</span>
                                            </div>
                                        </td>
                                        <td style="width: 50%; padding: 0; vertical-align: top;">
                                            <div class="info-field">
                                                <span class="info-field-label">Prénom:</span>
                                                <span class="info-field-value">{{ $user->firstname }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                
                                <div class="info-field">
                                    <span class="info-field-label">Email:</span>
                                    <span class="info-field-value">{{ $user->email }}</span>
                                </div>

                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="width: 50%; padding: 0; vertical-align: top;">
                                            <div class="info-field">
                                                <span class="info-field-label">Cycle:</span>
                                                <span class="info-field-value">{{ $cycle->name }}</span>
                                            </div>
                                        </td>
                                        <td style="width: 50%; padding: 0; vertical-align: top;">
                                            <div class="info-field">
                                                <span class="info-field-label">Filière:</span>
                                                <span class="info-field-value">{{ $filiere->name }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="width: 50%; padding: 0; vertical-align: top;">
                                            <div class="info-field">
                                                <span class="info-field-label">Année:</span>
                                                <span class="info-field-value">{{ $classe->year }} année</span>
                                            </div>
                                        </td>
                                        <td style="width: 50%; padding: 0; vertical-align: top;">
                                            <div class="info-field">
                                                <span class="info-field-label">Contact:</span>
                                                <span class="info-field-value">{{ $user->phone }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <!-- ✨ FIN DE LA MODIFICATION ✨ -->
                                
                                <!-- QR Code avec positionnement absolu -->
                                @if(isset($qrCodeDataUri) && $qrCodeDataUri)
                                    <div style="position: absolute; bottom: 12mm; right: 2mm;">
                                        <img src="{{ $qrCodeDataUri }}" alt="QR Code RR" style="width: 25pt; height: 25pt;" />
                                    </div>
                                @endif

                            </td>
                        </tr>
                    </table>

                    <div class="year-banner">ANNEE : 2021 - 2022</div>
                </div>

                <!-- VERSO -->
                <div class="student-card verso">
                    <div class="magnetic-stripe"></div>
                    <div class="verso-content">
                        <div class="signature-panel">
                            <span class="signature-panel-label">Signature du titulaire</span>
                        </div>
                        <div class="card-info-text">
                            <p>Cette carte est strictement personnelle et doit être présentée à toute réquisition.</p>
                            <p>En cas de perte, veuillez la retourner au service de la scolarité de l'EPUMA.</p>
                            <hr>
                            <strong>www.epuma.lephenix.bj</strong>
                        </div>
                        <img src="{{ public_path('storage/logo-epuma.png') }}" alt="Logo" class="logo-verso" />
                    </div>
                </div>
            </td>
            <!-- === FIN CARTE 1 === -->

            <!-- === CARTE 2 (Exemple) === -->
            <td>
                <!-- ... Recto et Verso de la carte 2 ... -->
            </td>
        </tr>
    </table>

</body>
</html>