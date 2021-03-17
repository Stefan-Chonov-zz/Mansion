$(document).ready(function () {
    let group = {};
    let map = {};

    $("#findNearestPostsForm").on('submit', function (event) {
        event.preventDefault();
        event.stopPropagation();

        let $postcode = '';
        let $radius = '';
        const $inputs = $('#findNearestPostsForm').serializeArray();
        $.each($inputs, function (index, input) {
            if (input['name'] === 'postcode') {
                $postcode = input['value'].toUpperCase();
            }

            if (input['name'] === 'radius') {
                $radius = input['value'];
            }
        });

        removeUrlParam('postcode');
        removeUrlParam('radius');
        $('#postcode').next().hide();

        if ($postcode.trim() !== '') {
            $('#postcode').css('border-color', '#ced4da');
            if (!ukPostCodePatternValidation($postcode)) {
                $('#postcode').css('border-color', 'red');
                $('.error-message').show();
            } else {
                insertUrlParam('postcode', $postcode);
                if ($radius.trim() !== '' && $radius !== '0' && $radius !== '' && $.isNumeric($radius)) {
                    insertUrlParam('radius', $radius);
                }

                removeAllMarkersFromMap(map);
                loadMarkersOnMap(map);
            }
        }
    });

    $("#clearButton").on('click', function (event) {
        event.stopPropagation();
        event.preventDefault();

        $(':input', '#findNearestPostsForm')
            .not(':button, :submit, :reset')
            .val('')
            .prop('checked', false)
            .prop('selected', false);

        $('#postcode').css('border', '1px solid #ced4da');

        let $dropDownRadiusItems = $('#radiusDropdown li');
        $dropDownRadiusItems.removeClass('disabled');
        $($dropDownRadiusItems[0]).addClass('disabled');
        $('#radiusButton').text($($dropDownRadiusItems[0]).text());

        removeUrlParam('postcode');
        removeUrlParam('radius');

        removeAllMarkersFromMap(map);
        loadMarkersOnMap(map);
    });

    $('#radiusDropdown li').on('click', function () {
        $('#radiusDropdown li').removeClass('disabled');
        $(this).addClass('disabled');

        $('#radius').val($(this).data('value'));
        $('#radiusButton').text($(this).text());
    });

    /**
     * Checks UK Postcode Pattern tailored by UK Government Data Standard.
     *
     * @param $postcode UK Postcode.
     * @returns {boolean}
     */
    function ukPostCodePatternValidation($postcode) {
        const $postcodePattern = new RegExp('^([A-Z]{1,2}\\d[A-Z\\d]? ?\\d[A-Z]{2}|GIR ?0A{2})$');

        return $postcodePattern.test($postcode);
    }

    /**
     * Append parameter to url.
     *
     * @param key Parameter name.
     * @param value Parameter value.
     */
    function insertUrlParam(key, value) {
        if (history.pushState) {
            let searchParams = new URLSearchParams(window.location.search);
            searchParams.set(key, value);
            let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + searchParams.toString();
            window.history.pushState({path: newurl}, '', newurl);
        }
    }

    /**
     * Remove parameter from url.
     *
     * @param key Parameter name.
     */
    function removeUrlParam(key) {
        if (history.pushState) {
            let searchParams = new URLSearchParams(window.location.search);
            searchParams.delete(key);
            let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + searchParams.toString();
            window.history.pushState({path: newurl}, '', newurl);
        }
    }

    /**
     * Retrieves posts.
     */
    async function getPosts() {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        if (urlParams.has('postcode')) {
            let radius = 0;
            if (urlParams.has('radius')) {
                radius = urlParams.get('radius');
            }

            return await findNearestUKPosts(urlParams.get('postcode'), radius);
        }

        return await getAllUKPosts();
    }

    /**
     * Retrieves nearest UK posts.
     *
     * @param {string} postcode Postcode
     * @param {number} radius Radius
     */
    async function findNearestUKPosts(postcode, radius) {
        return await $.get('http://localhost:8000/home/findNearestUKPosts', {'postcode': postcode, 'radius': radius});
    }

    /**
     * Retrieves all UK posts.
     */
    async function getAllUKPosts() {
        return await $.get("http://localhost:8000/home/getUKPosts");
    }

    /**
     * Load map.
     */
    function loadMap() {
        let platform = new H.service.Platform({
            apikey: window.apikey
        });
        let defaultLayers = platform.createDefaultLayers();

        group = new H.map.Group();
        let map = new H.Map(document.getElementById('map'), defaultLayers.vector.normal.map, {
            center: {lat: window.center_latitude, lng: window.center_longitude},
            zoom: 9,
            pixelRatio: window.devicePixelRatio || 1
        });

        window.addEventListener('resize', () => map.getViewPort().resize());

        new H.mapevents.Behavior(new H.mapevents.MapEvents(map));

        H.ui.UI.createDefault(map, defaultLayers);

        return map;
    }

    /**
     * Load markers on map.
     *
     * @param  {H.Map} map A HERE Map instance within the application
     */
    function loadMarkersOnMap(map) {
        getPosts().then(function (posts) {
            if (posts) {
                posts = $.parseJSON(posts);
                $.each(posts['data'], function (index, post) {
                    addMarkerToMap(map, post['latitude'], post['longitude']);
                });
            }
        });
    }

    /**
     * Adds markers to the map highlighting the locations of the captials of
     * France, Italy, Germany, Spain and the United Kingdom.
     *
     * @param  {H.Map} map  A HERE Map instance within the application
     * @param {number} $lat Latitude
     * @param {number} $lng Longitude
     */
    function addMarkerToMap(map, $lat, $lng) {
        const marker = new H.map.Marker({lat: $lat, lng: $lng});
        group.addObject(marker);
        map.addObject(group);
    }

    /**
     * Remove all markers from the map.
     *
     * @param  {H.Map} map      A HERE Map instance within the application.
     */
    function removeAllMarkersFromMap(map) {
        group.removeAll();
    }

    map = loadMap();
    loadMarkersOnMap(map);
});