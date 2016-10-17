@include('admin.Public_header')
<frameset rows="50,*" framespacing="0" border="0">
    <frame id="top_nav" name="top_nav" src="{{ route('Admin::Index::topNav') }}" frameborder="no" scrolling="no"/>
    <frameset rows="*" cols="220,*" framespacing="0" border="0">
        <frame id="left_nav" name="left_nav" src="{{ route('Admin::Index::leftNav') }}"
               frameborder="no" scrolling="yes"/>
        <frame id="main" name="main" src="{{ route('Admin::Index::main') }}" frameborder="no" scrolling="yes"/>
    </frameset>
</frameset>
</html>