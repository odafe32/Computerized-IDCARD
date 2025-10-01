hello

<form method="POST" action="{{ route('logout') }}" class="d-inline">
    @csrf
    <button type="submit" class="btn btn-danger btn-sm">
        <i class="mdi mdi-logout"></i> Logout
    </button>
</form>
