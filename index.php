<?php
// Orijinal resim yolunu belirleyin
$originalImagePath = 'image.jpg';

// Resmi yükle
$originalImage = imagecreatefromjpeg($originalImagePath);
$originalWidth = imagesx($originalImage);
$originalHeight = imagesy($originalImage);

// Parça genişliği ve yüksekliği hesapla
$pieceWidth = $originalWidth / 3;
$pieceHeight = $originalHeight / 3;

// Puzzle resmi için boş bir alan oluştur
$puzzleImage = imagecreatetruecolor($originalWidth, $originalHeight);

// Rastgele sıralama için indeksler oluştur
$indexes = range(0, 8);
// shuffle($indexes);

// Belirli sıralama
$specifiedOrder = [3, 7, 1, 5, 0, 4, 6, 8, 2];

// Düzenleme işlemini gerçekleştir
$reorderedImageDataArray = [];
foreach ($specifiedOrder as $index) {
    $reorderedImageDataArray[] = $indexes[$index];
}
error_log( print_r($indexes, true) );


// Parçaları kes ve puzzle'a yerleştir (rastgele sıralama)
$index = 0;
for ($row = 0; $row < 3; $row++) {
    for ($col = 0; $col < 3; $col++) {
        $pieceImage = imagecreatetruecolor($pieceWidth, $pieceHeight);
        $sourceX = ($reorderedImageDataArray[$index] % 3) * $pieceWidth;
        $sourceY = floor($reorderedImageDataArray[$index] / 3) * $pieceHeight;
        imagecopy($pieceImage, $originalImage, 0, 0, $sourceX, $sourceY, $pieceWidth, $pieceHeight);
        imagecopy($puzzleImage, $pieceImage, $col * $pieceWidth, $row * $pieceHeight, 0, 0, $pieceWidth, $pieceHeight);
        imagedestroy($pieceImage);
        $index++;
    }
}

// Puzzle resmini oluştur ve sakla
ob_start();
imagejpeg($puzzleImage);
$imageData = ob_get_clean();

// Veriyi base64 kodla
$base64Image = base64_encode($imageData);

// Veri URI oluştur
$dataUri = 'data:image/jpeg;base64,' . $base64Image;

// Belleği temizle
imagedestroy($originalImage);
imagedestroy($puzzleImage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Puzzle Picture</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        canvas {
            border: 1px solid #000;
        }
    </style>
</head>
<body>
       <!-- Canvas elementi -->
       <canvas id="canvas" width="800" height="800"></canvas>
    
       <script>
        // Base64 formatındaki resim verisi
        var base64ImageData = "<?php echo $dataUri ?>";  // Örn. "data:image/jpeg;base64,...."
        var specifiedOrder = [4, 2, 8, 0, 5, 3, 6, 1, 7];
        
        // Resmi canvas üzerinde görüntüle
        var image = new Image();
        image.src = base64ImageData;
        image.onload = function() {
            var pieceWidth = image.width / 3;
            var pieceHeight = image.height / 3;
            
            var canvas = document.getElementById("canvas");
            canvas.width = image.width;
            canvas.height = image.height;
            
            var ctx = canvas.getContext("2d");
            
            var pieces = [];
            
            // Resmi 9 parçaya bölelim
            for (var row = 0; row < 3; row++) {
                for (var col = 0; col < 3; col++) {
                    var pieceCanvas = document.createElement("canvas");
                    pieceCanvas.width = pieceWidth;
                    pieceCanvas.height = pieceHeight;
                    var pieceCtx = pieceCanvas.getContext("2d");
                    pieceCtx.drawImage(image, col * pieceWidth, row * pieceHeight, pieceWidth, pieceHeight, 0, 0, pieceWidth, pieceHeight);
                    pieces.push(pieceCanvas);
                }
            }
            
            // Belirtilen sıraya göre parçaları düzenle ve canvas'a çiz
            for (var i = 0; i < specifiedOrder.length; i++) {
                var index = specifiedOrder[i];
                var row = Math.floor(i / 3);
                var col = i % 3;
                ctx.drawImage(pieces[index], col * pieceWidth, row * pieceHeight);
            }
        };
    </script>

</body>
</html>