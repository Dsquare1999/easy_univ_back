<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Liste des Étudiants - EPUMA</title>
    <style>
        @page {
            margin: 15mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            line-height: 1.4;
            font-size: 10pt;
            margin: 0;
        }

        /** 🏛️ Le conteneur principal. */
        .transcript-container {
            width: 100%;
        }

        /** 🔱 L'en-tête, reconstruit avec une table à 3 colonnes pour un contrôle total. */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #333;
            padding-bottom: 1rem;
            text-align: center;
        }
        .header-table td {
            vertical-align: middle;
        }
        .header-table h2, .header-table h3, .header-table h4, .header-table p {
            margin: 2px 0;
        }
        .header-table h2 { font-size: 1.1em; }
        .header-table h3 { font-size: 1em; }
        .header-table h4 { font-size: 0.9em; font-style: italic;}
        .logo-placeholder {
            border: 1px dashed #999;
            width: 80px;
            height: 80px;
            text-align: center;
            line-height: 80px;
            color: #999;
            font-size: 0.8em;
            margin: 0 auto;
        }
        .header-center-cell { width: 60%; }

        /** 📜 Le corps. */
        .main-title {
            text-align: center;
            font-size: 1.4em;
            font-weight: bold;
            text-decoration: underline;
            margin: 1.5rem 0;
        }

        /** 👤 Les infos de l'étudiant, maintenant une table à 2 colonnes. */
        .student-info-table {
            width: 100%;
            margin-bottom: 2rem;
        }
        .student-info-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 1rem;
        }
        .student-info-table p {
            margin: 3px 0;
        }

        .container {
            width: 100%;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #333;
            padding-bottom: 1rem;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .header-table td {
            vertical-align: middle;
        }
        
        .header-table h2, .header-table h3, .header-table h4, .header-table p {
            margin: 2px 0;
        }
        
        .header-table h2 { font-size: 1.1em; }
        .header-table h3 { font-size: 1em; }
        .header-table h4 { font-size: 0.9em; font-style: italic; }

        .class-info {
            margin: 20px 0;
            text-align: center;
        }

        .class-info p {
            margin: 5px 0;
        }


        /* Le chaos des signatures, dompté par une table. */
        .signatures-table {
            width: 100%;
            /* min-height: 150px; -- Ne fonctionne pas bien, on gère la hauteur avec le contenu */
        }
        .signatures-table td {
            width: 33.33%;
            text-align: right;
            vertical-align: bottom;
            position: relative; /* Contexte pour les tampons */
        }
        .signature-placeholder {
            margin-top: 3rem;
            font-style: italic;
            color: #555;
        }
        .director-signature { margin-bottom: 3rem; }
        .certification-box {
            border: 2px solid #333;
            padding: 10px;
            margin: 0.5rem auto;
            width: 90%;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .students-table th, .students-table td {
            border: 1px solid #333;
            padding: 6px 8px;
            font-size: 9pt;
        }

        .students-table th {
            background-color: #f8f8f8;
            font-weight: bold;
            text-align: center;
        }
        .footer-contact { 
            text-align: center; 
            margin-top: 2rem; 
            font-size: 0.8em; 
            border-top: 1px solid #ccc; 
            padding-top: 0.5rem; 
        }

        .transcript-footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            padding: 10px 0;
            font-size: 9pt;
        }

        .page-number {
            width: 100%;
            text-align: right;
            font-size: 8pt;
        }

        .page-number:after {
            content: counter(page);
        }

    </style>
</head>
<body>

    <div class="transcript-container">
        
        <!-- 🔱 En-tête -->
        <table class="header-table">
            <tr>
                <td style="width: 20%;"><div class="logo-placeholder">
                    <img src="{{ public_path('storage/logo-mesrs.png') }}" alt="Logo EPUMA" class="logo-epuma" style="max-width: 100%; height: auto; object-fit: cover;"/>
                </div></td>
                <td class="header-center-cell">
                    <h2>REPUBLIQUE DU BENIN</h2>
                    <p>Ministère de l'Enseignement Supérieur et de la Recherche Scientifique</p>
                    <h3>ECOLE POLYTECHNIQUE UNIVERSITAIRE DES METIERS D'AVENIR (EPUMA)</h3>
                    <h4>Le Phénix</h4>
                </td>
                <td style="width: 20%;"><div class="logo-placeholder">
                    <img src="{{ public_path('storage/logo-epuma.png') }}" alt="Logo EPUMA" class="logo-epuma" style="max-width: 100%; height: auto; object-fit: cover;"/>
                </div></td>
            </tr>
        </table>

        <main class="transcript-body">
            <h1 class="main-title">LISTE DES ÉTUDIANTS</h1>

            <!-- 👤 Infos de l'étudiant -->
            <table class="student-info-table">
                <tr>
                    <td>
                        <p><strong>Cycle :</strong> {{ $cycle->name }}</p>
                        <p><strong>Filière :</strong> {{ $filiere->name }}</p>
                    </td>
                    <td>
                        <p><strong>Année :</strong> {{ $classe->year }}</p>
                        <p><strong>Année universitaire :</strong> {{ $classe->academic_year }}-{{ $classe->academic_year + 1 }}</p>
                    </td>
                </tr>
            </table>

            <!-- Tableau des étudiants -->
            <table class="students-table">
                <thead>
                    <tr>
                        <th style="width: 5%">N°</th>
                        <th style="width: 15%">Nom</th>
                        <th style="width: 15%">Prénoms</th>
                        <th style="width: 12%">Matricule</th>
                        <th style="width: 8%">Sexe</th>
                        <th style="width: 20%">Email</th>
                        <th style="width: 12%">Téléphone</th>
                        <th style="width: 13%">Titre</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $index => $student)
                    <tr>
                        <td style="text-align: center">{{ $index + 1 }}</td>
                        <td>{{ $student['lastname'] }}</td>
                        <td>{{ $student['firstname'] }}</td>
                        <td style="text-align: center">{{ $student['matricule'] }}</td>
                        <td style="text-align: center">{{ $student['sexe'] }}</td>
                        <td>{{ $student['email'] }}</td>
                        <td>{{ $student['phone'] }}</td>
                        <td>{{ $student['titre'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <!-- Pied de page -->
        </main>

        <!-- 🖋️ Pied de page -->
        <footer class="transcript-footer">
            <table class="signatures-table">
                <tr>
                    <td>
                        <div class="signature-placeholder director-signature">
                            Le Sécrétariat
                            <br>
                            Document généré le {{ date('d/m/Y') }}
                        </div>
                        <p><strong>M. ASSONGBA S. Anicet</strong><br>Directeur de EPUMA le Phénix</p>
                    </td>
                </tr>
            </table>
            
            <p class="footer-contact">Le Phénix/Ecole Polytechnique Universitaire des Métiers d'Avenir (EPUMA). <br>Email: contact@epuma.lephenix.bj</p>
            <div class="page-number">
                Page 
            </div>
        </footer>
    </div>
</body>
</html>