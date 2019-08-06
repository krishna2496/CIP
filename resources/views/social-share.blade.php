<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <title>Facebook Sharing</title>
    
    @if (!is_null($mission))
        <meta property="og:url" content="{{route('social-sharing', ['fqdn' => $fqdn, 'missionId' => $missionId, 'langId' => $langId])}}" />
        <meta property="og:type" content="article" />
        @if ($mission->missionLanguage->count() > 0)
            <meta property="og:title" content="{{$mission->missionLanguage->first()->title}}" />
            <meta property="og:description" content="{{$mission->missionLanguage->first()->short_description}}" />
        @endif
        <meta property="og:image" content="{{$mission->missionMedia->first()->media_image}}" />
        <meta http-equiv="refresh" content="5;url=http://web8.anasource.com/team4/ciplatform/mission-detail/{{$mission->mission_id}}">    
    @else
        <meta http-equiv="refresh" content="5;url=http://{{$fqdn}}{{config('constants.FRONT_HOME_URL')}}">
    @endif
</head>
<body>
    <div class="row">
        <div class="text-center col-md-12">
            <h3> Please wait after 5 seconds page will be redirect </h3>
            <span>If not redirecting. Please click <a href="http://web8.anasource.com/team4/ciplatform/mission-detail/{{$mission->mission_id}}">here</a></span>
        </div>
    </div>
</body>
<style>
    .row .col-md-12 {
        float: left;
        width: 100%;
    }
    .text-center {
        text-align: center;
    }
</style>
</html>
