<div class="w-1/5 bg-[#222] text-white p-6 flex flex-col justify-start flex-shrink-0 min-h-screen shadow-lg">
        <!-- Logo Box -->
        <div class="bg-white w-[100px] h-[100px] flex justify-center items-center rounded-full overflow-hidden shadow-lg mt-8 mx-auto border-4 mb-15 border-[#800080]">
            <img src="./assets/img/logo.jpg" alt="Gym Logo" class="max-w-full max-h-full object-contain block mx-auto" />
        </div>

        <!-- Sidebar Menu -->
        <ul class="list-none p-0 text-base font-semibold space-y-4 mt-8">
            <li>
                <a href="index.php?page=dashboard" class="flex items-center gap-8 p-3 rounded-lg hover:bg-[#800080] hover:text-white transition-all duration-200 cursor-pointer">
                    <div class="material-icons mr-2">dashboard</div>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="index.php?page=members" class="flex items-center gap-8 p-3 rounded-lg hover:bg-[#800080] hover:text-white transition-all duration-200 cursor-pointer">
                    <div class="material-icons mr-2">group</div>
                    Members
                </a>
            </li>
            <li>
                <a href="index.php?page=payments" class="flex items-center gap-8 p-3 rounded-lg hover:bg-[#800080] hover:text-white transition-all duration-200 cursor-pointer">
                    <div class="material-icons mr-2">payment</div>
                    Payments
                </a>
            </li>
            <li>
                <a href="index.php?page=staff_attendance" class="flex items-center gap-8 p-3 rounded-lg hover:bg-[#800080] hover:text-white transition-all duration-200 cursor-pointer">
                    <div class="material-icons mr-2">event_available</div>
                    Staff Attendance
                </a>
            </li>
            <li>
                <a href="index.php?page=training_schedule" class="flex items-center gap-8 p-3 rounded-lg hover:bg-[#800080] hover:text-white transition-all duration-200 cursor-pointer">
                    <div class="material-icons mr-2">schedule</div>
                    Training Schedule
                </a>
            </li>
            <li>
                <a href="logout.php" class="flex items-center gap-8 p-3 rounded-lg hover:bg-red-700 hover:text-white transition-all duration-200 cursor-pointer">
                    <div class="material-icons mr-2">logout</div>
                    Logout
                </a>
            </li>
        </ul>
</div>