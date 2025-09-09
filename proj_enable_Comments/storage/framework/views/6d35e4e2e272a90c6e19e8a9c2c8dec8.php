<html>
    <head>
        <title>Welcome to the Blog</title>
        <style>
            body {
                font-family: Arial;
                background: #f2f2f2;
                text-align: center;
                padding: 100px;
            }

            .container{
                background: white;
                padding: 40px;
                border-radius: 10px;
                display: inline-block;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            h1 {
                margin-bottom:20px;
            }
            .btn{
                padding: 10px 25px;
                margin: 10px;
                text-decoration: none;
                border-radius: 5px;
                background-color: #3490dc;
                color: white;
                font-weight: bold;
            }
            .btn:hover{
                background-color: #1234bd;
            }


        </style>
    </head>
    <body>
        <div class="container">
            <h1>Welcome to Our Blog Platform</h1>
            <p>Kindly, Login or Register to explore posts by category</p>
            <a href ="<?php echo e(route('login')); ?>" class="btn">Login</a>
            <a href="<?php echo e(route('register')); ?>" class="btn">Register</a>
        </div>
    </body>
</html>
<?php /**PATH D:\Mi.tasks\proj_enable_Comments\resources\views/welcome.blade.php ENDPATH**/ ?>