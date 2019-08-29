@extends('layout')

@section('content')
<h1>Testing</h1>
 <?php if (isset($accessToken)) { ?>
    
    <div class="list-group-item">
      <p class="list-group-item-heading"><?php echo $accessToken; ?></p>
      
    </div>
    <?php  
    } ?>
@endsection