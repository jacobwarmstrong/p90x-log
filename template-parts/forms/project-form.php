<?php
//project-form.php
//this file has our html form for inputting project information.
?>
<form class="form-signin-heading p-3" method="post" action="procedures/insert.php" name="<?php //echo $class; ?>" >
    <div class="form-group">
        <label for="name" class="control-label">Project Name</label>
            <input type="hidden" name="ownerId" value="<?php echo findUserByAccessToken()['userId'] ?>" >
            <input type="text" class="form-control" id="name" name="name" placeholder="Describe Your Project As A Title" value=""required autofocus>
    </div>
    <div class="form-group">
        <label for="description" class="control-label">Project Scope</label>
            <textarea class="form-control" id="description" name="description" placeholder="Write up a brief overview of the project."></textarea>
    </div>
    <label class="control-label">Location</label>
    <?php addressFormInputs(); ?>
    <label class="control-label">Deadline</label>
    <div class="container">
        <div class="form-group row">
          <label for="deadline" class="sr-only">Deadline</label>
          <input type="date" name="deadline" class="form-control col md-6" value="">
        </div>
    </div>
    <div class="form-group">
        <label for="clientSelect" class="control-label">Client</label>
            <select class="custom-select" name="clientId">
                <?php clientSelectForm($clientName); ?>
            </select>
    </div>
    <button class="btn btn-primary" type="submit">Add <?php //echo $class; ?></button>
</form>