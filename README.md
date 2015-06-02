VerteXVaaR.BlueSprints
======================

Quickstart & Documentation ;)
-----------------------------

The "create-project" way (more quick):

    composer create-project vertexvaar/bluesprints=dev-master
    cd bluesprints
    php -S localhost:8000 -t public/

The "require" way (uses BlueSprints as library):

    mkdir bluesprints
    cd bluesprints
    composer require 'vertexvaar/bluesprints:dev-master' 'vertexvaar/bluewelcome:dev-master'
    [ENTER] #(when asked if you want to copy folders)
    php -S localhost:8000 -t public/

open http://localhost:8000 in your favorite browser
