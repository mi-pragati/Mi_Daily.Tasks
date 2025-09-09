<h1>Admin Login<h1>

<form action='/admin' method="POST">
    @csrf
    {{method_field('PUT')}}
    <input type="text" name="user" placeholder="Enter Admin Name"/>
<br>
<br>
    <input type="Password" name="Password" placeholder="enter your password"/>
    <br>
    <br>
<button>Login</button>
</form>
