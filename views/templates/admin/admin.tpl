

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">Products loading process</div>
                <div class="panel-body">
                    <div class="progress">
                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
                            <span class="sr-only">40% Complete (success)</span>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <button name="submitbluxedrop" class="btn btn-default pull-right" id="configuration_form_submit_btn" type="submit" value="1">
                        <i class="process-icon-update"></i> Load
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>


<script>
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this);
        }
    };
    xhttp.open("GET", "http://drop.novaengel.com/api/products/availables/4b791d7a-76e3-4b9b-ab5c-9bc8669bdf6d/es", true);
    xhttp.setRequestHeader('Access-Control-Allow-Origin', null );
    xhttp.setRequestHeader('Origin', null );
    xhttp.setRequestHeader('content-type', 'application/json' );
    xhttp.send();
</script>