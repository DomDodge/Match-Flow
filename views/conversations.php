<div class="internalHeader" ng-app="peopleApp" ng-controller="PeopleController">
    <h2>Conversations</h2>
    <div class="peopleHolder" ng-show="!modal && !viewing">
        <div class="person">
            <h4 ng-click='toggleModal()'>Add new</h4>
        </div>
        <div class="person" ng-repeat="p in people">
            <i class="fa-solid fa-eye" ng-click="setView(p)"></i>    
            <h4>{{ p.name }}</h4> 
            <h4>status: {{ p.status }}</h4>
            <h4>last interaction: {{ p.last_interation }} </h4>
            <h4>next interaction: {{ p.next_interaction }}</h4>
        </div>
    </div>

    <div class="newPersonModal" ng-if="modal">
        <h3>Add Person</h3>

        <input type="text" ng-model="newPerson.name" placeholder="Name" required>
        <input type="text" ng-model="newPerson.contact" placeholder="Phone / Contact" required>
        <textarea ng-model="newPerson.notes" placeholder="Notes"></textarea>
        {{newPerson.status = "Conversation"}}
        <button ng-click="insertUser()">Submit</button>
        <button ng-click="toggleModal()">Close</button>
    </div>

    <div class="viewPersonModal" ng-if="viewing">
        <h3>{{currentPerson.name}}</h3>
        <h4>{{currentPerson.contact_info}}</h4>
        <h4>{{currentPerson.status}}</h4>
        <h4>{{currentPerson.notes}}</h4>

        <button ng-click="closeView()">Close</button>
    </div>
</div>

<script>
    const app = angular.module("peopleApp", []);

    app.controller("PeopleController", function($scope, $http) {
        $scope.modal = false;
        $scope.viewing = false;
        $scope.currentPerson = {};
        $scope.people = <?php echo json_encode(getPeopleWithStatus($_SESSION['username'], 'Conversation')); ?>;

        $scope.toggleModal = function() {
            $scope.modal = !$scope.modal;

            if ($scope.modal) {
                $scope.newPerson = {};
            }
        }

        $scope.insertUser = function() {

            $http.post("/inc/add_person.php", $scope.newPerson)
            .then(function(response) {
                console.log("SUCCESS: " + JSON.stringify(response.data));
            })
            .catch(function(err) {
                console.log("ERROR: " + JSON.stringify(err));
            });

            $scope.modal = false;
        }

        $scope.setView = function(person) {
            $scope.viewing = true;
            $scope.currentPerson = person;
        }

        $scope.closeView = function() {
            $scope.viewing = false;
            $scope.currentPerson = {};
        }
    });
</script>