<div class="internalHeader" ng-app="activitiesApp" ng-controller="ActivitiesController">
    <h2>Activities</h2>
    <div class="peopleHolder" ng-show="!newActivity">
        <div class="person" style="justify-content: flex-start; gap: 20px;">
            <h4 id="newBtn" ng-click='newActivity = !newActivity'>New</h4>
            <input class="searchBar" placeholder="Search..." ng-model="searchText">
        </div>
        <div class="person" ng-repeat="a in activities | filter:searchByName">    
            <h4>{{ a.title }}</h4> 
            <h4>{{ getPersonOffID(a.person_id) }}</h4>
            <p>{{ a.description }}</p>
            <h4>Event Date: {{ a.event_date | date:'mediumDate' }} </h4>
        </div>
    </div>

    <!-- New Activity -->
    <div class="newPersonModal" ng-if="newActivity">
        <h3>Let's Plan Somthing</h3>

        <input type="text" ng-model="plan.title" placeholder="Title" required>
        <textarea ng-model="plan.description" placeholder="Write out a description..." required></textarea>
        <input type="date" ng-model="plan.date" required min="{{ today | date:'yyyy-MM-dd' }}">

        <label>People: </label>
        <select multiple
            size="5"
            ng-model="plan.person"
            ng-options="p.id as p.name for p in people"
            style="width: 100%; padding: 5px; border: 1px solid #ccc; border-radius: 4px;">
        </select>

        <div class="submitActions">
            <button ng-click="newActivity = !newActivity">Close</button>
            <button ng-click="saveActivity()">Submit</button>
        </div>
    </div>
</div>

<script>
    const app = angular.module("activitiesApp", []);

    app.controller("ActivitiesController", function($scope, $http) {
        $scope.newActivity = false;
        $scope.today = new Date();
        $scope.activities = <?php echo json_encode(getActivities($_SESSION['username'])); ?>;
        $scope.people = <?php echo json_encode(getPeople($_SESSION['username'])); ?>

        console.log($scope.activities);

        $scope.plan = { person: [] };

        $scope.saveActivity = function() {
            const payload = {
                title: $scope.plan.title,
                description: $scope.plan.description,
                event_date: $scope.plan.date,
                person_id: JSON.stringify($scope.plan.person)
            };

            $http.post('/inc/save_activity.php', payload)
                .then(response => {
                    alert('Activity saved!');
                    $scope.plan = { person: [] };
                }, error => {
                    console.error(error);
                    alert('Error saving activity');
                });
        };

        $scope.getPersonOffID = function (pid) {
            if (!pid || !$scope.people) return "Not Specified";

            const ids = Array.isArray(pid) ? pid : [pid];

            const names = [];

            for (let i = 0; i < ids.length; i++) {
                for (let j = 0; j < $scope.people.length; j++) {
                    if ($scope.people[j].id == ids[i]) {
                        names.push($scope.people[j].name);
                        break;
                    }
                }
            }

            return names.length ? names.join(", ") : "Not Specified";
        };
    })

</script>