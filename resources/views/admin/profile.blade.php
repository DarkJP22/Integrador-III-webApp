@extends('layouts.admins.app')

@section('content')

    
    <section class="content">
      
      <div class="row">
        <div class="col-md-4">

            <avatar-form :user="{{ $profileUser }}"></avatar-form>
         
        </div>
        <!-- /.col -->
        <div class="col-md-8">
         
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs" id="tabs-profile">
              <li class="{{ isset($tab) ? ($tab =='profile') ? 'active' : '' : 'active' }}"><a href="#profile" data-toggle="tab" class="tab-profile">Perfil</a></li>
              <li class="{{ isset($tab) ? ($tab =='loginAs') ? 'active' : '' : '' }}"><a href="#loginAs" data-toggle="tab" class="tab-loginAs">Login como</a></li>

            </ul>
            <div class="tab-content">
              <div class="{{ isset($tab) ? ($tab =='profile') ? 'active' : '' : 'active' }} tab-pane" id="profile">

                 @include('admin._profileForm')

              </div>
              <div class="{{ isset($tab) ? ($tab =='loginAs') ? 'active' : '' : '' }} tab-pane" id="loginAs">

                <form action="/admin/loginas" method="POST" class="form-horizontal">
                  @csrf
                  <div class="form-group">
                    <div class="col-sm-6">
                      <input type="text" name="user_id" class="form-control" placeholder="Usuario ID">
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-6">
                      <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                  </div>
                 
                </form>

             </div>
             
             
             
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </section>


@endsection

