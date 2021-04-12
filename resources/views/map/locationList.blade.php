<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">

        <title>Laravel</title>

    </head>
    <body>
        @if ( count($locations) )
            @foreach ( $locations as $location )
                <?php 
                    $id = $location->id;
                    $name = $location->name;
                    $description = $location->description;
                ?>
                @component('map.unit.showLocationDetail')
                    @slot('id')
                        {{ $id }}
                    @endslot
                    @slot('name')
                        {{ $name }}
                    @endslot
                    @slot('description')
                        {{ $description }}
                    @endslot
                @endcomponent
            @endforeach
        @endif
    </body>
</html>
