<?php
$title = 'Create New Story | TravelBlog';
$description = 'Share your amazing travel story with the world.';
include 'includes/header.php';
?>

<?php if($message == "success"): ?>
    <div class="success-msg">🚀 Story Published Successfully!</div>
    <script>setTimeout(()=> window.location.href="index.php", 2000);</script>
<?php elseif(strpos($message, "error:") === 0): ?>
    <div class="error-msg"><?php echo str_replace("error:", "❌", $message); ?></div>
<?php endif; ?>

<div class="wrapper">
    <div class="form-side">
        <a href="index.php" style="text-decoration:none; color:var(--primary); font-weight:700; margin-bottom:20px; display:inline-block;">
            <i class="fas fa-arrow-left"></i> Back to Feed
        </a>
        <h2>Share Your Story</h2>
        <p>Every journey has a soul. Tell yours to the world.</p>

        <form method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <label>Catchy Title</label>
                <input type="text" id="titleInput" name="title" placeholder="e.g. A Sunset in Santorini" required>
            </div>

            <div class="input-group">
                <label>Story Details</label>
                <textarea id="descInput" name="description" rows="6" placeholder="Start writing your adventure..." required></textarea>
                <div class="char-counter" id="charCounter">0 / 1000 characters</div>
            </div>

            <div class="input-group">
                <label>Featured Image</label>
                <div class="upload-area" onclick="document.getElementById('fileInput').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p id="fileName">Click to upload or drag and drop</p>
                    <span style="font-size:0.8rem; color:#94a3b8;">PNG, JPG up to 10MB</span>
                </div>
                <input type="file" id="fileInput" name="image" hidden required accept="image/*">
            </div>

            <button type="submit" name="submit" class="btn-publish">🚀 Publish Story</button>
        </form>
    </div>

    <div class="preview-side">
        <div class="preview-card" id="cardWrap">
            <img id="prevImg" class="preview-img" src="https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=800&q=60">
            <div class="preview-content">
                <div style="color:var(--primary); font-weight:800; font-size:0.7rem; text-transform:uppercase; margin-bottom:10px;">Preview Mode</div>
                <h3 id="prevTitle">Your Title Here</h3>
                <p id="prevDesc">Your story will start appearing here as you type. Make it inspiring!</p>
                <div style="margin-top:20px; display:flex; align-items:center; gap:10px; opacity:0.6;">
                    <i class="far fa-heart"></i> <i class="far fa-comment"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
    const titleInput = document.getElementById('titleInput');
    const descInput = document.getElementById('descInput');
    const fileInput = document.getElementById('fileInput');
    const charCounter = document.getElementById('charCounter');
    const maxChars = 1000;

    // Live preview for title
    titleInput.addEventListener('input', function() {
        document.getElementById('prevTitle').textContent = this.value || 'Your Title Here';
    });

    // Live preview for description with character counter
    descInput.addEventListener('input', function() {
        const count = this.value.length;
        charCounter.textContent = `${count} / ${maxChars} characters`;

        // Update preview
        document.getElementById('prevDesc').textContent = this.value.substring(0, 150) + (this.value.length > 150 ? '...' : '') || 'Your story will start appearing here as you type. Make it inspiring!';

        // Color coding
        charCounter.classList.remove('warning', 'danger');
        if (count > 800) {
            charCounter.classList.add('warning');
        }
        if (count > 950) {
            charCounter.classList.add('danger');
        }
    });

    // File upload preview
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            document.getElementById('fileName').innerText = file.name;
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('prevImg').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>