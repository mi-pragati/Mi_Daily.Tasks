<html>
    <head>

    <title><?php echo e($post->title); ?></title>
    <style>
        body{font-family:Arial; background: #f0f0f0; padding: 30px; }
        .post {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: auto;
        }

        .meta {
            font-size:0.8em;
            color: #666;
        }
    </style>
    </head>
    <body>
    <div class="post">
        <h1><?php echo e($post->title); ?></h1>
        <div class="meta">
            Category: <?php echo e($post->category->name); ?> | posted on: <?php echo e($post->created_at->format('M d, Y h: i A')); ?>

</p>
<p><?php echo e($post->content); ?></p>
<p><a href="/"> <- Back to Home</a></p>
        </div>
    </div>
    </body>
</html>
<?php /**PATH D:\Mi.tasks\proj_user.profile_Change\resources\views\posts\show.blade.php ENDPATH**/ ?>