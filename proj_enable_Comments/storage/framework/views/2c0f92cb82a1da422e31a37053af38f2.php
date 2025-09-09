<?php $__env->startSection('content'); ?>
<div class="container">
    <h1>Edit Post</h1>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('posts.update', $post->id)); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="<?php echo e(old('title', $post->title)); ?>" required>
        </div>

        <div class="mb-3">
            <label for="category_id" class="form-label">Category</label>
            <select name="category_id" class="form-control" required>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($category->id); ?>" <?php echo e($post->category_id == $category->id ? 'selected' : ''); ?>>
                        <?php echo e($category->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea name="content" class="form-control" rows="5" required><?php echo e(old('content', $post->content)); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="media" class="form-label">Upload Image/Video</label>
            <input type="file" name="media" class="form-control">

            <?php if($post->media): ?>
                <p class="mt-2">Current Media:</p>
                <?php if(Str::endsWith($post->media, ['.jpg','.jpeg','.png','.gif'])): ?>
                    <img src="<?php echo e(asset('storage/media/' . $post->media)); ?>" width="200" alt="Post media">
                <?php elseif(Str::endsWith($post->media, ['.mp4','.webm'])): ?>
                    <video width="320" height="240" controls>
                        <source src="<?php echo e(asset('storage/media/' . $post->media)); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Update Post</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Mi.tasks\proj_enable_Comments\resources\views\posts\edit.blade.php ENDPATH**/ ?>