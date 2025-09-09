<h1>UserLogin<h1>

<form action='/users' method="POST">
    @csrf
    <input type="text" name="user" placeholder="Enter User Name"/>
<br>
<br>
    <input type="Password" name="Password" placeholder="enter your password"/>
    <br>
    <br>
<button>Login</button>
</form>
