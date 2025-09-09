<?php
    use Illuminate\Support\Str;
?>



<?php $__env->startSection('content'); ?>
<h1>All Posts</h1>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<a href="<?php echo e(route('posts.create')); ?>" class="btn btn-primary mb-3">Create New Post</a>

<?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title"><?php echo e($post->title); ?></h5>
            <p class="card-text"><?php echo e($post->content); ?></p>

            <p><strong>Category:</strong> <?php echo e($post->category?->name ?? 'Uncategorized'); ?></p>
            <p><strong>Author:</strong> <?php echo e($post->user->name); ?></p>

            
            <?php if($post->media_path): ?>
                <?php
                    $extension = pathinfo($post->media_path, PATHINFO_EXTENSION);
                ?>

                <?php if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                    <img src="<?php echo e(asset('storage/' . $post->media_path)); ?>" alt="Post Media" style="max-width: 400px;">
                <?php elseif(in_array($extension, ['mp4', 'mov', 'avi'])): ?>
                    <video controls width="400">
                        <source src="<?php echo e(asset('storage/' . $post->media_path)); ?>" type="video/<?php echo e($extension); ?>">
                        Your browser does not support the video tag.
                    </video>
                <?php else: ?>
                    <p><em>Unsupported media type.</em></p>
                <?php endif; ?>
            <?php else: ?>
                
                <img src="<?php echo e(asset('images/default-placeholder.png')); ?>" alt="No media" style="max-width: 400px;">
            <?php endif; ?>

            <a href="<?php echo e(route('posts.edit', $post->id)); ?>" class="btn btn-warning mt-3">Edit</a>

            <form action="<?php echo e(route('posts.destroy', $post->id)); ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button class="btn btn-danger mt-3">Delete</button>
            </form>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Mi.tasks\proj_user.profile_Change\resources\views\posts\index.blade.php ENDPATH**/ ?>