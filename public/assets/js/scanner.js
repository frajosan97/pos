$("#start-scanner").click(function () {
    if (isScanning) return;
    isScanning = true;

    Quagga.init({
        inputStream: {
            type: "LiveStream",
            constraints: {
                facingMode: "environment", // Use back camera
                width: 640,
                height: 480
            },
            target: document.querySelector("#scanner-container")
        },
        locator: {
            patchSize: "medium",
            halfSample: true
        },
        decoder: {
            readers: ["ean_reader", "code_128_reader"]
        }, // Supports multiple barcode types
        locate: true
    }, function (err) {
        if (err) {
            console.error("Scanner initialization failed:", err);
            isScanning = false;
            return;
        }
        console.log("Scanner started");
        Quagga.start();
        $("#stop-scanner").show();
    });

    Quagga.onProcessed(function (result) {
        let drawingCanvas = Quagga.canvas.dom.overlay;
        let ctx = drawingCanvas.getContext("2d");
        if (result) {
            if (result.boxes) {
                ctx.clearRect(0, 0, drawingCanvas.width, drawingCanvas.height);
                result.boxes.forEach((box) => {
                    Quagga.ImageDebug.drawPath(box, {
                        x: 0,
                        y: 1
                    }, ctx, {
                        color: "green",
                        lineWidth: 2
                    });
                });
            }
        }
    });

    Quagga.onDetected(function (result) {
        let code = result.codeResult.code;
        $("#barcode-result").text(code);
        $('#scannerModal').modal('hide');
        $("#barcode").val(code);
        searchProduct(code);
        Quagga.stop();
        $("#stop-scanner").hide();
        isScanning = false;
    });
});

$("#stop-scanner").click(function () {
    $('#scannerModal').modal('hide');
    Quagga.stop();
    $("#stop-scanner").hide();
    isScanning = false;
});