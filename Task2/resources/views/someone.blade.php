<h1> Anyone Login</h1>
<form action='/someone' method="POST">
    @csrf
    {{method_field('DELETE')}}
<input type="text" name="someone" placeholder="Enter Someone's Name"/>
<br>
<br>
<input type ="Password" name="password" placeholder="Enter your Password"/>
<br>
<br>
<button>LogIn</button>
</form>

