<div class="internalHeader">
    <h2>Welcome <?php echo getFirstName(e($_SESSION['username'])); ?>!</h2>
    <div class="iconList">
        <div class="icon">
            <h3>Conversations</h3>
            <ul class='keyIndicators'>
                <li><?php echo getGoalCount($_SESSION['username'], date('W') - 1, date('o'), 'conversations', 'progress_count') ?></li>
                <li class="current"><?php echo getGoalCount($_SESSION['username'], date('W'), date('o'), 'conversations', 'progress_count') ?></li>
                <li><?php echo getGoalCount($_SESSION['username'], date('W'), date('o'), 'conversations', 'target_count') ?></li>
            </ul>
        </div>

        <div class="icon">
            <h3>Connections</h3>
            <ul class='keyIndicators'>
                <li><?php echo getGoalCount($_SESSION['username'], date('W') - 1, date('o'), 'connections', 'progress_count') ?></li>
                <li class="current"><?php echo getGoalCount($_SESSION['username'], date('W'), date('o'), 'connections', 'progress_count') ?></li>
                <li><?php echo getGoalCount($_SESSION['username'], date('W'), date('o'), 'connections', 'target_count') ?></li>
            </ul>
        </div>

        <div class="icon">
            <h3>Friendships</h3>
            <ul class='keyIndicators'>
                <li><?php echo getGoalCount($_SESSION['username'], date('W') - 1, date('o'), 'friendships', 'progress_count') ?></li>
                <li class="current"><?php echo getGoalCount($_SESSION['username'], date('W'), date('o'), 'friendships', 'progress_count') ?></li>
                <li><?php echo getGoalCount($_SESSION['username'], date('W'), date('o'), 'friendships', 'target_count') ?></li>
            </ul>
        </div>

        <div class="icon">
            <h3>Dates</h3>
            <ul class='keyIndicators'>
                <li><?php echo getGoalCount($_SESSION['username'], date('W') - 1, date('o'), 'dates', 'progress_count') ?></li>
                <li class="current"><?php echo getGoalCount($_SESSION['username'], date('W'), date('o'), 'dates', 'progress_count') ?></li>
                <li><?php echo getGoalCount($_SESSION['username'], date('W'), date('o'), 'dates', 'target_count') ?></li>
            </ul>
        </div>
    </div>
</div>

<div class="lowerContainer">
    <div class="bigIcon">
        <h3>Monthly Actuals</h3>
        <canvas id='dataChart'>

        </canvas>
    </div>

    <div class="bigIcon">
        <h3>Monthly Goals</h3>
        <canvas id='goalsChart'>

        </canvas>       
    </div>
</div>

<div class="goalAdder">
    <div class="goal">
        <h4>Conversations</h4>
        <input class="goal-int" id="convo" type="number" value="<?php echo getGoalCount($_SESSION['username'], date('W'), date('o'), 'conversations', 'target_count'); ?>" min="0" max="10">
    </div>
    <div class="goal">
        <h4>Connections</h4>
        <input class="goal-int" id="conn" type="number" value="<?php echo getGoalCount($_SESSION['username'], date('W'), date('o'), 'connections', 'target_count'); ?>" min="0" max="10">
    </div>
    <div class="goal">
        <h4>Friendships</h4>
        <input class="goal-int" id="friend" type="number" value="<?php echo getGoalCount($_SESSION['username'], date('W'), date('o'), 'friendships', 'target_count'); ?>" min="0" max="10">
    </div>
    <div class="goal">
        <h4>Dates</h4>
        <input class="goal-int" id="dates" type="number" value="<?php echo getGoalCount($_SESSION['username'], date('W'), date('o'), 'dates', 'target_count'); ?>" min="0" max="10">
    </div>
    <button id="login" onclick="updateGoals()">
        GOALS
</button>
</div>

<!-- Goal Logic -->
<script>


    function updateGoals() {
        let conversations = Number(document.getElementById('convo').value || 0);
        let connections   = Number(document.getElementById('conn').value || 0);
        let friendships   = Number(document.getElementById('friend').value || 0);
        let dates         = Number(document.getElementById('dates').value || 0);

        const url = `../inc/update_goals.php?conversations=${conversations}&connections=${connections}&friendships=${friendships}&dates=${dates}`;

        fetch(url)
            .then(() => location.reload());
    }
</script>

<!-- Graph Logic -->
<script src="../script/chart.umd.js"></script>
<?php
    $labels = ['week1', 'week2', 'week3', 'week4'];
    sort($labels);

    $conversations = getMonthGoals($_SESSION['username'], 'conversations', 'progress_count');
    $connections = getMonthGoals($_SESSION['username'], 'connections', 'progress_count');
    $friendships = getMonthGoals($_SESSION['username'], 'friendships', 'progress_count');
    $dates = getMonthGoals($_SESSION['username'], 'dates', 'progress_count');

    $datasets = [
        [
            'label' => "Conversations",
            'data'  => $conversations,
            'fill'  => false,
            'borderColor' => 'rgb(75, 192, 192)',
            'tension' => 0.1
        ],
        [
            'label' => "Connections",
            'data'  => $connections,
            'fill'  => false,
            'borderColor' => 'rgb(255, 75, 192)',
            'tension' => 0.1
        ],
        [
            'label' => "Friendships",
            'data'  => $friendships,
            'fill'  => false,
            'borderColor' => 'rgba(247, 167, 62, 1)',
            'tension' => 0.1
        ],
        [
            'label' => "Dates",
            'data'  => $dates,
            'fill'  => false,
            'borderColor' => 'rgba(130, 255, 119, 1)',
            'tension' => 0.1
        ]
    ];

    $goal_conversations = getMonthGoals($_SESSION['username'], 'conversations', 'target_count');
    $goal_connections = getMonthGoals($_SESSION['username'], 'connections', 'target_count');
    $goal_friendships = getMonthGoals($_SESSION['username'], 'friendships', 'target_count');
    $goal_dates = getMonthGoals($_SESSION['username'], 'dates', 'target_count');

    $goal_datasets = [
        [
            'label' => "Conversations",
            'data'  => $goal_conversations,
            'fill'  => false,
            'borderColor' => 'rgb(75, 192, 192)',
            'tension' => 0.1
        ],
        [
            'label' => "Connections",
            'data'  => $goal_connections,
            'fill'  => false,
            'borderColor' => 'rgb(255, 75, 192)',
            'tension' => 0.1
        ],
        [
            'label' => "Friendships",
            'data'  => $goal_friendships,
            'fill'  => false,
            'borderColor' => 'rgba(247, 167, 62, 1)',
            'tension' => 0.1
        ],
        [
            'label' => "Dates",
            'data'  => $goal_dates,
            'fill'  => false,
            'borderColor' => 'rgba(130, 255, 119, 1)',
            'tension' => 0.1
        ]
    ];
?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('dataChart');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: <?php echo json_encode($datasets); ?>
            },
            options: {
                scales: {
                    y: {
                        min: 0,
                        max: 10,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return Number.isInteger(value) ? value: null;
                            }
                        }
                    }
                }
            }
        });

        const ctx2 = document.getElementById('goalsChart');
        const chart2 = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: <?php echo json_encode($goal_datasets); ?>
            },
            options: {
                scales: {
                    y: {
                        min: 0,
                        max: 10,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return Number.isInteger(value) ? value: null;
                            }
                        }
                    }
                }
            }
        });
    });
</script>