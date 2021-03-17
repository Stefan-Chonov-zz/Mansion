<?php require_once __DIR__ . '/../Partial/header.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center">Find Nearest UK Posts</h2>
        <form id="findNearestPostsForm" class="mt-5 mb-5">
            <div class="row justify-content-center">
                <div class="col-2">
                    <input type="text"
                           id="postcode"
                           name="postcode"
                           class="form-control"
                           placeholder="UK Postcode"
                           value="<?php echo isset($data['postcode']) ? $data['postcode'] : '' ?>"/>
                    <div class="error-message text-center">Invalid UK Postcode!</div>
                </div>
                <div class="col-auto">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle"
                                type="button"
                                id="radiusButton"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                            <?php echo !empty($data['radius']) ? $data['dropDownRadius'][$data['radius']] : $data['dropDownRadius'][0]; ?>
                        </button>
                        <ul id="radiusDropdown" class="dropdown-menu">
                            <?php
                            if (isset($data['dropDownRadius'])) {
                                foreach ($data['dropDownRadius'] as $key => $value) {
                                    if (($data['radius'] === '' && $key == 0) || $data['radius'] == $key) {
                                        ?>
                                        <li class="dropdown-item disabled" data-value="<?php echo $key; ?>"><?php echo $value; ?></li>
                                        <?php
                                    } else {
                                        ?>
                                        <li class="dropdown-item" data-value="<?php echo $key; ?>"><?php echo $value; ?></li>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </ul>
                    </div>
                    <input type="hidden"
                           id="radius"
                           name="radius"
                           value="<?php echo isset($data['radius']) ? $data['radius'] : 0 ?>"/>
                </div>
                <div class="col-auto">
                    <button type="submit" id="ukPostsFormSubmit" class="btn btn-primary">Find</button>
                </div>
                <div class="col-auto">
                    <button type="button" id="clearButton" class="btn btn-success">Clear</button>
                </div>
            </div>
        </form>
        <div id="map" class="justify-content-center"></div>
    </div>

<?php require_once __DIR__ . '/../Partial/footer.html'; ?>