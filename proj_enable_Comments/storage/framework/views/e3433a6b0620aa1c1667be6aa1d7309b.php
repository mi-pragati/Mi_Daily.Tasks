<?php $__env->startSection('content'); ?>
    <div class="banner">
        <div>
            <h1>Welcome to my blog</h1>
            <p>Discover Posts By Category Below!!</p>
        </div>
    </div>

    <div class="categories">
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="category">
                <a href="<?php echo e(route('category.posts', $category->id)); ?>">
                    <h3><?php echo e($category->name); ?></h3>
                </a>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ffb6c1;
            margin: 0;
        }

        .banner {
            background: url('https://png.pngtree.com/background/20221206/original/pngtree-simple-aesthetic-background-with-hand-drawn-leaves-picture-image_1984683.jpg') no-repeat center center;
            background-size: cover;
            height: 300px;
            color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            font-size: 2em;
            text-align: center;
        }

        .categories {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 30px;
        }

        .category {
            background: white;
            border-radius: 10px;
            margin: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
            text-align: center;
            width: 200px;
        }

        .category:hover {
            transform: scale(1.05);
            background-color: rgba(0,0.1,0,0.1);
        }
    </style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Mi.tasks\proj_enable_Comments\resources\views/home.blade.php ENDPATH**/ ?>