<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de Préinscription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #ecf2fe;
            overflow: hidden;
        }
        .container {
            width: 100%;
            max-width: 700px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: left;
            margin-bottom: 20px;
        }
        .header img {
            width: 150px; 
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .student{
            width: 100%;
            background-color: #fff;
            padding: 20px;
        }
        .student-info {
            width: 60%;
            display: inline-block;
            margin-top: 20px;
        }
        .student-profile{
            width: 30%;
            display: inline-block;

        }
        .student-profile img{
            width: 100%;
            height: auto;
            object-fit: cover;
            object-position: center center;
        }
        .student-info p {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
        }
        .section-title {
            font-weight: bold;
            font-size: 18px;
            color: #333;
            margin-top: 30px;
        }
        .table-info {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
        }
        .table-info th, .table-info td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .table-info th {
            background-color: #f5f5f5;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #888;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path('storage/logo_easy_univ_bleu.svg') }}" alt="Logo Easy Univ">
        </div>

        <h1>Fiche de Préinscription</h1>

        <div class="student">
            <div class="student-info">
                <p><strong>Nom de l'étudiant :</strong> {{ $user->name }} </p>
                <p><strong>Email :</strong> {{ $user->email }}</p>
                <p><strong>Titre :</strong> {{ $student->titre }} </p>
                <p><strong>Statut :</strong> {{ $student->statut }} </p>
                <p><strong>Date de Préinscription :</strong> {{ $student->created_at }} </p>
            </div>
            <div class="student-profile">
                <img src="{{ public_path('storage/'.$user->profile) }}" alt="Profile image">
            </div>
        </div>
        

        <div class="section-title">Détails de l'inscription</div>
        <table class="table-info">
            <tr>
                <th>Cycle</th>
                <td> {{ $cycle->name }} </td>
            </tr>
            <tr>
                <th>Filière</th>
                <td> {{ $filiere->name }}</td>
            </tr>
            <tr>
                <th>Année</th>
                <td> {{ $classe->year }}</td>
            </tr>
        </table>

        <div class="section-title">Aucunes informations supplémentaires</div>
        <p>Veuillez conserver cette fiche de préinscription pour vos dossiers. Elle contient des informations importantes sur votre inscription à l'université. Si vous avez des questions, veuillez contacter notre service d'administration.</p>

        <div class="footer">
            <p>EasyUniv - Tous droits réservés © {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>
