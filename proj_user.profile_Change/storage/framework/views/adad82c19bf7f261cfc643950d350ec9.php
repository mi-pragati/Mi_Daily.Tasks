<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-6 bg-white rounded shadow">
    <h2 class="text-2xl mb-4">Create New Post</h2>

    <?php if($errors->any()): ?>
        <div class="mb-4 text-red-600">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>- <?php echo e($error); ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('posts.store')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="mb-3">
        <label for="media" class="form-label">Upload Image/Video</label>
        <input type="file" name="media" class="form-control" accept="image/*,video/*">
        <small class="form-text text-muted">Accepted formats: jpg, png, gif, mp4, webm</small>
    </div>
        <div class="mb-4">
            <label>Title</label>
            <input type="text" name="title" class="w-full p-2 border rounded" required>
        </div>

        <div class="mb-4">
            <label>Content</label>
            <textarea name="content" rows="5" class="w-full p-2 border rounded" required></textarea>
        </div>

        <div class="mb-4">
            <label>Category</label>
            <select name="category_id" class="w-full p-2 border rounded">
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <button type="submit" class="bg-blue-600 text-black px-4 py-2 rounded hover:bg-blue-700">
    Create
</button>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Mi.tasks\proj_user.profile_Change\resources\views\posts\create.blade.php ENDPATH**/ ?>