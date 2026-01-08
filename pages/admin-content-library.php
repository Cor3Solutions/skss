<?php
$page_title = "Content Library";
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

$message = '';
$error = '';

// Create uploads directory if it doesn't exist
$upload_dir = '../assets/uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_upload'])) {
    $file = $_FILES['file_upload'];
    $file_category = $_POST['file_category'];
    
    if ($file['error'] === 0) {
        $allowed_types = [
            'video' => ['mp4', 'mov', 'avi', 'webm'],
            'pdf' => ['pdf'],
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp']
        ];
        
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $original_name = pathinfo($file['name'], PATHINFO_FILENAME);
        
        // Validate file type
        $type_valid = false;
        foreach ($allowed_types as $types) {
            if (in_array($file_ext, $types)) {
                $type_valid = true;
                break;
            }
        }
        
        if ($type_valid) {
            // Generate unique filename
            $new_filename = $file_category . '_' . time() . '_' . preg_replace('/[^a-z0-9_-]/i', '', $original_name) . '.' . $file_ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $message = "File uploaded successfully: $new_filename";
            } else {
                $error = "Failed to upload file. Check directory permissions.";
            }
        } else {
            $error = "Invalid file type. Allowed: " . implode(', ', array_merge(...array_values($allowed_types)));
        }
    } else {
        $error = "Upload error: " . $file['error'];
    }
}

// Handle file deletion
if (isset($_GET['delete'])) {
    $filename = basename($_GET['delete']);
    $file_path = $upload_dir . $filename;
    
    if (file_exists($file_path) && unlink($file_path)) {
        $message = "File deleted successfully!";
    } else {
        $error = "Failed to delete file.";
    }
}

// Get all uploaded files
$files = [];
if (is_dir($upload_dir)) {
    $scan = scandir($upload_dir);
    foreach ($scan as $file) {
        if ($file !== '.' && $file !== '..') {
            $file_path = $upload_dir . $file;
            $files[] = [
                'name' => $file,
                'size' => filesize($file_path),
                'modified' => filemtime($file_path),
                'type' => strtolower(pathinfo($file, PATHINFO_EXTENSION))
            ];
        }
    }
    // Sort by modified date (newest first)
    usort($files, function($a, $b) {
        return $b['modified'] - $a['modified'];
    });
}

include '../includes/header.php';
?>

<style>
.admin-page {
    background: linear-gradient(135deg, #f8f6f3 0%, #ffffff 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.page-header {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
    border-left: 4px solid var(--color-primary);
}

.content-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
}

.file-item {
    background: var(--color-bg-light);
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border-left: 4px solid var(--color-secondary);
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
}

.file-item:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.file-type-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-right: 1rem;
}

.type-video { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.type-pdf { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
.type-image { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }

.upload-zone {
    border: 3px dashed var(--color-border);
    border-radius: 12px;
    padding: 3rem;
    text-align: center;
    background: var(--color-bg-light);
    transition: all 0.3s ease;
}

.upload-zone:hover {
    border-color: var(--color-secondary);
    background: white;
}
</style>

<div class="admin-page">
    <div class="container">
        <div class="page-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="font-size: 2rem; margin-bottom: 0.5rem;">Content Library</h1>
                    <p style="color: var(--color-text-light);">Upload and manage videos, PDFs, and images for courses</p>
                </div>
                <a href="dashboard.php" class="btn btn-outline">← Back to Dashboard</a>
            </div>
        </div>

        <?php if ($message): ?>
            <div style="background: #e8f5e9; color: #2e7d32; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                ✅ <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div style="background: #ffebee; color: var(--color-primary); padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                ❌ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Upload Form -->
        <div class="content-card">
            <h3 style="margin-bottom: 1.5rem; color: var(--color-primary); font-size: 1.5rem;">
                Upload New File
            </h3>

            <form method="POST" enctype="multipart/form-data">
                <div class="upload-zone">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">📤</div>
                    <h4 style="margin-bottom: 1rem;">Choose a file to upload</h4>
                    <p style="color: var(--color-text-light); margin-bottom: 1.5rem;">
                        Supported: Videos (MP4, MOV, AVI, WEBM), PDFs, Images (JPG, PNG, GIF, WEBP)
                    </p>

                    <div style="display: flex; gap: 1rem; justify-content: center; align-items: center; margin-bottom: 1.5rem;">
                        <select name="file_category" class="form-select" required style="max-width: 200px;">
                            <option value="">Select Category...</option>
                            <option value="course">Course Material</option>
                            <option value="training">Training Video</option>
                            <option value="technique">Technique Guide</option>
                            <option value="document">Document</option>
                            <option value="certificate">Certificate</option>
                            <option value="general">General</option>
                        </select>

                        <input type="file" name="file_upload" required class="form-input" accept=".mp4,.mov,.avi,.webm,.pdf,.jpg,.jpeg,.png,.gif,.webp">
                    </div>

                    <button type="submit" class="btn btn-primary" style="font-size: 1rem; padding: 0.75rem 2rem;">
                        Upload File
                    </button>
                </div>
            </form>

            <div style="margin-top: 2rem; padding: 1rem; background: #fff3e0; border-radius: 8px;">
                <h4 style="margin-bottom: 0.5rem;">💡 How to Use Uploaded Files:</h4>
                <ul style="margin: 0; padding-left: 1.5rem; color: var(--color-text-light);">
                    <li>After uploading, copy the filename</li>
                    <li>In Course Management, paste the filename as the content URL</li>
                    <li>Format: <code>assets/uploads/filename.ext</code></li>
                    <li>Example: <code>assets/uploads/course_1234567890_intro.mp4</code></li>
                </ul>
            </div>
        </div>

        <!-- Files List -->
        <div class="content-card">
            <h3 style="margin-bottom: 1.5rem; color: var(--color-primary); font-size: 1.5rem;">
                Uploaded Files (<?php echo count($files); ?>)
            </h3>

            <?php if (count($files) > 0): ?>
                <?php foreach ($files as $file): ?>
                    <div class="file-item">
                        <div style="display: flex; align-items: center; flex: 1;">
                            <div class="file-type-icon type-<?php 
                                if (in_array($file['type'], ['mp4', 'mov', 'avi', 'webm'])) echo 'video';
                                elseif ($file['type'] === 'pdf') echo 'pdf';
                                else echo 'image';
                            ?>">
                                <?php 
                                if (in_array($file['type'], ['mp4', 'mov', 'avi', 'webm'])) echo '🎥';
                                elseif ($file['type'] === 'pdf') echo '📄';
                                else echo '🖼️';
                                ?>
                            </div>

                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 0.5rem 0; font-size: 1rem;"><?php echo htmlspecialchars($file['name']); ?></h4>
                                <p style="margin: 0; font-size: 0.875rem; color: var(--color-text-light);">
                                    <strong>Size:</strong> <?php echo number_format($file['size'] / 1024 / 1024, 2); ?> MB | 
                                    <strong>Type:</strong> <?php echo strtoupper($file['type']); ?> |
                                    <strong>Uploaded:</strong> <?php echo date('M d, Y H:i', $file['modified']); ?>
                                </p>
                                <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem;">
                                    <strong>Path:</strong> 
                                    <code style="background: white; padding: 0.25rem 0.5rem; border-radius: 4px;">
                                        assets/uploads/<?php echo $file['name']; ?>
                                    </code>
                                    <button onclick="navigator.clipboard.writeText('assets/uploads/<?php echo $file['name']; ?>')" style="background: var(--color-secondary); color: white; border: none; padding: 0.25rem 0.75rem; border-radius: 4px; cursor: pointer; margin-left: 0.5rem;">
                                        Copy
                                    </button>
                                </p>
                            </div>
                        </div>

                        <div style="display: flex; gap: 0.5rem;">
                            <a href="../assets/uploads/<?php echo $file['name']; ?>" target="_blank" 
                               style="background: var(--color-secondary); color: white; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; font-size: 0.875rem;">
                                View
                            </a>
                            <a href="?delete=<?php echo $file['name']; ?>" 
                               onclick="return confirm('Delete this file permanently?');"
                               style="background: var(--color-primary); color: white; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; font-size: 0.875rem;">
                                Delete
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; padding: 3rem; color: var(--color-text-light);">
                    No files uploaded yet. Upload your first file above.
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
