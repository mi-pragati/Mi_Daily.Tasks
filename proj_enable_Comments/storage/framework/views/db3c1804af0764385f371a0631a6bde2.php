<?php $__env->startSection('content'); ?>
<div class="container">
    <h1>Edit Profile</h1>

    <?php if(session('status') === 'profile-updated'): ?>
        <div class="alert alert-success">Profile updated successfully!</div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('profile.update')); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PATCH'); ?>

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input id="name" name="name" class="form-control" type="text" value="<?php echo e(old('name', $user->name)); ?>" required autofocus>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" class="form-control" type="email" value="<?php echo e(old('email', $user->email)); ?>" required>
        </div>

        <button class="btn btn-primary">Save</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Mi.tasks\proj_enable_Comments\resources\views\profile\edit.blade.php ENDPATH**/ ?>