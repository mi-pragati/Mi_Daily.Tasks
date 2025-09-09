<?php use Illuminate\Support\Str; ?>

<html>
    <head>
        <title>Posts in <?php echo e($category->name); ?></title>
        <style>
            body { font-family: Arial, background:#f8f8f8; padding: 20px;}
            .post {
                background: rgba(0.1,0,0,0.1);
                padding: 15px;
                margin-bottom: 20px;
                border-radius: 8px;
                box-shadow: 0 0 5px rgba(0,0,0,0.1);
            }
            h2 {color: #444;}
        </style>
    </head>
    <body>
        <h1>Posts in <?php echo e($category->name); ?></h1>
        <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="post">
            <h2><?php echo e($post->title); ?></h2>
            <p><small>Posted On <?php echo e($post->created_at->format('M d, Y')); ?></small></p>
            <p><?php echo e(Str::limit($post->content, 150)); ?></p>
            <a href="<?php echo e(route('posts.show',$post->id)); ?>">Read More -> </a>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p> No Posts available in this category.</p>
        <?php endif; ?>
    </body>
</html>
<?php /**PATH D:\Mi.tasks\proj_enable_Comments\resources\views/posts/by_category.blade.php ENDPATH**/ ?>