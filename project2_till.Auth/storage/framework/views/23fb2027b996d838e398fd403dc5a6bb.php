<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
</head>
<body>
    <h1>Welcome, <?php echo e($user->name); ?></h1>
    <p>Email: <?php echo e($user->email); ?></p>

    <h2>Create a New Post</h2>
    <form method="POST" action="<?php echo e(route('profile.posts.store')); ?>">
        <?php echo csrf_field(); ?>
        <input type="text" name="title" placeholder="Post title" required><br>
        <textarea name="content" placeholder="Post content" required></textarea><br>
        <select name="category_id">
            <?php $__currentLoopData = \App\Models\Category::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select><br>
        <button type="submit">Create Post</button>
    </form>

    <h2>Your Posts</h2>
    <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <h3><?php echo e($post->title); ?></h3>
            <p><?php echo e($post->content); ?></p>

            <a href="<?php echo e(route('profile.posts.edit', $post->id)); ?>">Edit</a> |
            <form method="POST" action="<?php echo e(route('profile.posts.destroy', $post->id)); ?>" style="display:inline;">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" onclick="return confirm('Delete this post?')">Delete</button>
            </form>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</body>
</html>
<?php /**PATH D:\Mi.tasks\project2_till.Auth\resources\views\profile.blade.php ENDPATH**/ ?>