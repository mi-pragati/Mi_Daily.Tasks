<html>
    <head>
        <title>Simple Blog</title>
        <style>
            body { font-family:Arial: background: white; padding:20px }
            .post {background:cream; padding: 15px; margin-bottom:20px; border-radius:8px;}
            h2 {color: magenta;}
            .category {font-size: 0.9em; color: brown;}
            </style>
    </head>
<body>
<h1>Blog Posts</h1>

<?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<div class="post">

<h2><?php echo e($post->title); ?></h2>

<p class="category">Category:

<?php echo e($post->category->name); ?></p>

<p><?php echo e($post->content); ?></p>

</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</body>
</html>
<?php /**PATH D:\Mi.tasks\project2_till.Auth\resources\views\posts\index.blade.php ENDPATH**/ ?>