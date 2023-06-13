<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Onito | Genre Subtotals</title>

    <style>
        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px 5px 10px 5px;
        }

        .body {
            max-width: max-content;
            margin: 25px auto 25px auto;
        }
    </style>
</head>

<body class="body">
    <table>
        <thead>
            <tr>
                <th>Genre</th>
                <th>primaryTitle</th>
                <th>numVotes</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($genreVotes as $genreVote)
                @php
                    $count = 0;
                @endphp
                @foreach ($genreVote->getMovieVotes() as $movieVote)
                    @php
                        $count++;
                    @endphp
                    <tr>
                        <td>{{ $genreVote->getGenre() }}</td>
                        <td>{{ $movieVote->getPrimaryTitle() }}</td>
                        <td>{{ $movieVote->getNumVotes() }}</td>
                    </tr>
                @endforeach
                @if ($count > 0)
                    <tr>
                        <td></td>
                        <th>TOTAL</th>
                        <td>{{ $genreVote->getTotalVotes() }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>

</html>
