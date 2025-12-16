<div class="internalHeader" ng-app="peopleApp" ng-controller="PeopleController">
    <h2>User Info</h2>
    <h3>First Name: <?php echo getFirstName(e($_SESSION['username'])); ?></h3>
    <h3>Last Name: <?php echo getLastName(e($_SESSION['username'])); ?></h3>
    <h3>Username: <?php echo e($_SESSION['username']); ?></h3>
</div>