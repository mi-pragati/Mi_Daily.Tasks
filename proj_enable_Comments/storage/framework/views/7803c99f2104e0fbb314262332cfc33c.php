<html>
    <head>
        <title><?php echo e($post->title); ?></title>
        <style>
            body {
                font-family: Arial;
                background: #f0f0f0;
                padding: 30px;
            }
            .post {
                background: white;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                max-width: 800px;
                margin: auto;
            }
            .meta {
                font-size: 0.8em;
                color: #666;
            }
            .comment {
                background: #fff;
                border-radius: 5px;
                padding: 10px;
                margin-bottom: 10px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }
            .comment-form textarea {
                width: 100%;
                border: 1px solid #ccc;
                border-radius: 5px;
                padding: 8px;
                resize: vertical;
            }
            .comment-form button {
                margin-top: 5px;
                padding: 8px 15px;
                background: #007bff;
                border: none;
                color: #fff;
                border-radius: 5px;
                cursor: pointer;
            }
            .comment-form button:hover {
                background: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class="post">
            <h1><?php echo e($post->title); ?></h1>
            <div class="meta">
                Category: <?php echo e($post->category->name ?? 'Uncategorized'); ?> |
                Posted on: <?php echo e($post->created_at->format('M d, Y h:i A')); ?>

            </div>
            <p><?php echo e($post->content); ?></p>
            <p><a href="/"> &larr; Back to Home</a></p>
        </div>

        
        <div class="post" style="margin-top: 20px;">

        <h3>Comments (<?php echo e($post->comments->count()); ?>)</h3>

<?php $__empty_1 = true; $__currentLoopData = $post->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div style="background: #fff; padding: 10px; margin-bottom: 10px; border-radius: 5px;">
        <strong><?php echo e($comment->name); ?></strong> <small>(<?php echo e($comment->email); ?>)</small><br>
        <p><?php echo e($comment->comment); ?></p>
        <small>Posted on <?php echo e($comment->created_at->format('M d, Y h:i A')); ?></small>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <p>No comments yet. Be the first to comment!</p>
<?php endif; ?>


            
            <div style="margin-top: 30px;">
    <h3>Leave a Comment</h3>
    <form action="<?php echo e(route('comments.store', $post->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <input type="text" name="name" placeholder="Your Name" required><br><br>
        <input type="email" name="email" placeholder="Your Email" required><br><br>
        <textarea name="comment" placeholder="Your Comment" required></textarea><br><br>
        <button type="submit">Submit Comment</button>
    </form>
</div>
        </div>
    </body>
</html>
<?php /**PATH D:\Mi.tasks\proj_enable_Comments\resources\views\posts\show.blade.php ENDPATH**/ ?>