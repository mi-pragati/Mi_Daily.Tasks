<h1>testing patch</h1>
<form action='/padmin' method="POST">
    @csrf
    {{method_field('PATCH')}}
    <input type="text" name="user" placeholder="Enter the padmin name"/>
    <br>
    <br>
    <input type="password" name="password" placeholder="Enter your password"/>
    <br>
    <br>
    <button>Submit It</button>
</form>
