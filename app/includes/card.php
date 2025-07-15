<div class="flex flex-wrap gap-4 mt-4 w-full">
    <div class="bg-[#222121] text-white p-4 py-10 rounded-lg shadow-md flex-1 min-w-[12rem] text-center border border-[#585757]">
        <div class="text-sm text-gray-400 mb-1">Total Members</div>
        <div class="text-2xl font-bold" id="totalMembers">Loading ...</div>
    </div>
    <div class="bg-[#222121] text-white p-4 py-10 rounded-lg shadow-md flex-1 min-w-[12rem] text-center border border-[#585757]">
        <div class="text-sm text-gray-400 mb-1">Total Active Members</div>
        <div class="text-2xl font-bold" id="totalActiveMembers">Loading ...</div>
    </div>
    <div class="bg-[#222121] text-white p-4 py-10 rounded-lg shadow-md flex-1 min-w-[12rem] text-center border border-[#585757]">
        <div class="text-sm text-gray-400 mb-1">Total Revenue</div>
        <div class="text-2xl font-bold" id="totalRevenue">Loading ...</div>
    </div>
    <div class="bg-[#222121] text-white p-4 py-10 rounded-lg shadow-md flex-1 min-w-[12rem] text-center border border-[#585757]">
        <div class="text-sm text-gray-400 mb-1">Available Trainers</div>
        <div class="text-2xl font-bold" id="totalAvailableTrainers"></div>
    </div>
</div>

<script>
    function fetchActiveMemberCount(){
        fetch('app/includes/activemembercount.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalActiveMembers').innerText = data.total_active_members !== undefined ? data.total_active_members : 'Loading...';
        })
        .catch(error => {
            console.error('Error fetching member count:', error);
            document.getElementById('totalActiveMembers').innerText = 'Error';
        });
    }
    
    fetchActiveMemberCount();

    setInterval(fetchActiveMemberCount, 5000); // Refresh every minute

    function fetchMemberCount(){
        fetch('app/includes/totalmembercount.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalMembers').innerText = data.total_members !== undefined ? data.total_members : 'Loading...';
        })
        .catch(error => {
            console.error('Error fetching member count:', error);
            document.getElementById('totalMembers').innerText = 'Error';
        });
    }
    
    fetchMemberCount();

    setInterval(fetchMemberCount, 5000); // Refresh every minute

    function fetchTotalRevenue(){
        fetch('app/includes/totalrevenue.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalRevenue').innerText = data.total_revenue !== undefined ? '₱' + data.total_revenue : 'Loading...';
        })
        .catch(error => {
            console.error(error);
            document.getElementById('totalRevenue').innerText = 'Error';
        });
    }
    
    fetchTotalRevenue();

    setInterval(fetchTotalRevenue, 5000); // Refresh every minute

    function fetchAvailableTrainers(){
        fetch('app/includes/availabletrainers.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalAvailableTrainers').innerText = data.total_active_trainers !== undefined ? data.total_active_trainers : 'Loading...';
        })
        .catch(error => {
            console.error('Error fetching available trainers:', error);
            document.getElementById('totalAvailableTrainers').innerText = 'Error';
        });
    }

    fetchAvailableTrainers();
    setInterval(fetchAvailableTrainers, 5000); // Refresh every minute  


</script>
<script>
    


</script>