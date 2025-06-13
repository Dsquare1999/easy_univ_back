<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relevé de Notes (Semestre 1)</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 960px;
            margin: 0 auto;
            padding: 20px;
            font-size: 10px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #4CAF50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
            font-size: 9px
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            width: 100px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{ public_path('storage/logo_easy_univ_bleu.svg') }}" alt="Logo Université">
        </div>
        <h2>Relevé de Notes (Semestre 1)</h2>
        <table>
            <thead>
                <tr>
                    <th>Nom & Prénom</th>
                    @foreach($matieres as $matiere)
                        <th>{{ $matiere->libelle }}</th>
                    @endforeach
                    <th>Décision</th>
                </tr>
            </thead>
            <tbody>
                @foreach($notes as $note)
                <tr>
                    <td>{{ $note['name'] }}</td>
                    @foreach($matieres as $matiere)
                        <td>{{ 
                            $note[$matiere->id] ?? '-' 
                        }}</td>
                    @endforeach
                    <td>{{ $note['decision'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
