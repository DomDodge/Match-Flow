<div class="internalHeader" ng-app="peopleApp" ng-controller="PeopleController">
    <h2>People</h2>
    <div class="peopleHolder" ng-show="!modal && !viewing && !statusMenu">
        <div class="person" style="justify-content: flex-start; gap: 20px;">
            <h4 id="newBtn" ng-click='toggleModal()'>New</h4>
            <input class="searchBar" placeholder="Search..." ng-model="searchText">
        </div>
        <div class="person" ng-repeat="p in people | filter:searchByName">
            <i class="fa-solid fa-eye" ng-click="setView(p)"></i>    
            <h4>{{ p.name }}</h4> 
            <div class="status" id="{{p.status}}" ng-click="changeStatus(p)">{{ p.status }}</div>
            <h4>last interaction: {{ getLastInteract(p.id) | date:'mediumDate' }} </h4>
            <h4>next interaction: {{ p.next_interaction | date:'mediumDate' }}</h4>
        </div>
    </div>

    <!-- New Person -->
    <div class="newPersonModal" ng-if="modal">
        <h3>Add Person</h3>

        <input type="text" ng-model="newPerson.name" placeholder="Name" required>
        <input type="text" ng-model="newPerson.contact" placeholder="Phone / Contact" required>
        <input type="date" ng-model="newPerson.date" required>
        <textarea ng-model="newPerson.notes" placeholder="Notes" required></textarea>

        <select ng-model="newPerson.status">
            <option value="Conversation">Conversation</option>
            <option value="Connection">Connection</option>
            <option value="Friendship">Friendship</option>
            <option value="Dating">Dating</option>
        </select>


        <div class="submitActions">
            <button ng-click="toggleModal()">Close</button>
            <button ng-click="insertUser()">Submit</button>
        </div>
    </div>

    <!-- Change Status -->
    <div class="changeStatusModal" ng-if="statusMenu">
        <h3>{{currentPerson.name}}</h3>
        <select ng-model="statusUpdate.status">
            <option value="Conversation">Conversation</option>
            <option value="Connection">Connection</option>
            <option value="Friendship">Friendship</option>
            <option value="Dating">Dating</option>
            <option value="Dropped">Dropped</option>
        </select>

         <h4>Date</h4>
        <input ng-model="statusUpdate.date" type="date">
        <h4>Note</h4>
        <textarea ng-model="statusUpdate.note"></textarea>

        <div class="submitActions">
            <button ng-click="changeStatus()">Close</button>
            <button ng-click="updateStatus(currentPerson.id)">Submit</button>
        </div>
    </div>

    <!-- View Person -->
    <div class="viewPersonModal" ng-if="viewing">
        <div class="left">
            <h2>{{currentPerson.name}}</h2>
            <h3>{{currentPerson.contact_info}}</h3>
            <h3 class="status" id="{{currentPerson.status}}" ng-click="changeStatus(currentPerson)">{{currentPerson.status}}</h3>
            <button ng-click="closeView()">Close</button>
        </div>
        <div class="right">
            <h3>Notes:</h3>
            <div class="note">
                <h4>Date</h4>
                <input ng-model="newNote.date" type="date">
                <h4>Note</h4>
                <textarea ng-model="newNote.note"></textarea>
                <button ng-click="addNote(currentPerson.id)">Add Note</button>
            </div>
            <div class="note" ng-repeat="n in notes[currentPerson.id]">
                <div class="noteHeader">
                    <h4 ng-if="!n.editing">{{n.note_date | date:'mediumDate'}}</h4>
                    <input ng-if="n.editing" type="date" ng-model="n.editDate">
                    <div class="actions">
                        <i ng-click="startEdit(n)" class="fa-solid fa-pencil"></i>
                        <i ng-click="changeNote(currentPerson.id, n.note, 'delete')" class="fa-solid fa-trash"></i>
                    </div>
                </div>                       
                <p ng-if="!n.editing">{{n.note}}</p>
                <textarea ng-if="n.editing" ng-model="n.editNote"></textarea>
                
                <div class="submitActions" ng-if="n.editing">
                    <button ng-click="n.editing = false">CANCEL</button>
                    <button ng-click="changeNote(currentPerson.id, n.note, 'update', n.editNote, n.editDate)">SAVE</button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const app = angular.module("peopleApp", []);

    app.controller("PeopleController", function($scope, $http) {
        $scope.modal = false;
        $scope.statusMenu = false;
        $scope.viewing = false;
        $scope.currentPerson = {};
        $scope.newNote = {};
        $scope.statusUpdate = {};
        $scope.people = <?php echo json_encode(getPeople($_SESSION['username'])); ?>;
        $scope.notes = <?php echo json_encode(getPeopleAndNotes($_SESSION['username'])); ?>;
        $scope.activities = <?php echo json_encode(getActivities($_SESSION['username'])); ?>;
        $scope.searchText = "";

        $scope.findNextInteraction = function () {
            $scope.people.forEach(function (p) {
                p.next_interaction = "Not Set";

                for (let i = $scope.activities.length - 1; i >= 0; i--) {
                    if ($scope.activities[i].person_id.includes(p.id)) {
                        p.next_interaction = $scope.activities[i].event_date;
                    }
                }
            });
        };

        $scope.findNextInteraction();

        $scope.startEdit = function(n) {
            n.editing = true;
            n.editNote = n.note;
            n.editDate = n.note_date;
        };

        $scope.toggleModal = function() {
            $scope.modal = !$scope.modal;

            if ($scope.modal) {
                $scope.newPerson = {};
                $scope.newPerson.status = "Conversation";
            }
        }

        $scope.searchByName = function(person) {
            if (!$scope.searchText) return true;
            return person.name.toLowerCase().includes($scope.searchText.toLowerCase());
        };

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

        $scope.addNote = function(pid) {
            $scope.newNote.id = pid;

            $http.post("/inc/add_note.php", $scope.newNote)
            .then(function(response) {
                console.log("SUCCESS: " + JSON.stringify(response.data));

                $scope.newNote = {};
            })
            .catch(function(err) {
                console.log("ERROR: " + JSON.stringify(err));
            });
        }

        $scope.updateStatus = function(pid) {
            $scope.statusUpdate.id = pid;

            $http.post("/inc/update_status.php", $scope.statusUpdate)
            .then(function(response) {
                console.log("SUCCESS: " + JSON.stringify(response.data));

                $scope.statusUpdate = {};
                $scope.currentPerson = {};
                $scope.statusMenu = false;

                window.location.reload();
            })
            .catch(function(err) {
                console.log("ERROR: " + JSON.stringify(err));
            });
        }
        $scope.changeNote = function(pid, note, action, newNote, date) {
            let data;

            if(action === 'delete') {
                data = {pid: pid, note: note, action: action};
            } else {
                data = {pid: pid, note: note, newNote: newNote, date: date, action: action};
            }
            
            $http.post("/inc/change_note.php", data)
            .then(function(response) {
                console.log("SUCCESS: " + JSON.stringify(response.data));
                window.location.reload();
            })
            .catch(function(err) {
                console.log("ERROR: " + JSON.stringify(err));
            });
        }

        $scope.changeStatus = function(person) {
            if (!$scope.statusMenu) {
                $scope.viewing = false;
                $scope.currentPerson = person;
                $scope.statusUpdate = {};
                $scope.statusUpdate.status = $scope.currentPerson.status
                $scope.statusMenu = true;
                
            } else {
                $scope.statusMenu = false;
                $scope.currentPerson = {};
                $scope.statusUpdate = {};
            }
        }


        $scope.setView = function(person) {
            $scope.viewing = true;
            $scope.currentPerson = person;
        }

        $scope.closeView = function() {
            $scope.viewing = false;
            $scope.currentPerson = {};
        }

        $scope.getLastInteract = function(pid) {
            const list = $scope.notes[pid];

            if (list && list.length > 0 && list[0].note_date) {
                return list[0].note_date;
            }

            return "None";
        }
    });
</script>