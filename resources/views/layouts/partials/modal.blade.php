<!-- Scanner Modal -->
<div class="modal fade" id="scannerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="scannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="scannerModalLabel">Bar Code Scanner</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-capitalize">
                <div id="scanner-container">
                    <video id="camera-feed" autoplay></video>
                    <div class="scanner-frame"></div>
                </div>
                <p>Scanned Code: <span id="barcode-result"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" id="start-scanner">Start Scanner</button>
                <button type="button" class="btn btn-outline-danger" id="stop-scanner">Stop Scanner</button>
            </div>
        </div>
    </div>
</div>