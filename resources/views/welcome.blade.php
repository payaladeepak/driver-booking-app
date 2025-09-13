{{-- resources/views/welcome.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>WowApp — Driver Booking (Demo)</title>
</head>
<body class="bg-[#FDFDFC] text-[#1b1b18] antialiased">
  <header class="max-w-6xl mx-auto px-6 py-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 bg-[#F53003] rounded flex items-center justify-center text-white font-bold">W</div>
      <div class="text-lg font-semibold">WowApp</div>
    </div>
    <nav class="space-x-4 hidden md:inline-flex">
      <a href="#features" class="text-sm">Features</a>
      <a href="#how" class="text-sm">How it works</a>
      <a href="#demo" class="text-sm">Demo</a>
    </nav>
    <div class="flex items-center gap-3">
      <a href="/" class="px-4 py-2 border border-[#1b1b18] rounded text-sm">Open App</a>
      <a href="#demo" class="px-4 py-2 bg-[#F53003] text-white rounded text-sm">Admin Login</a>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-6">
    <section class="grid lg:grid-cols-2 gap-8 items-center py-12">
      <div>
        <h1 class="text-4xl font-extrabold mb-4">Driver Booking — Simple, Fast, Reliable</h1>
        <p class="text-gray-600 mb-6">Create bookings, assign drivers, and manage the full lifecycle. Demo-ready on this host — try the admin and customer flows instantly.</p>
        <div class="flex gap-3">
          <a href="/" class="px-6 py-3 bg-[#F53003] text-white rounded shadow">Open Live App</a>
          <a href="#demo" class="px-6 py-3 border rounded">Admin Login</a>
        </div>

        <div id="demo" class="mt-8 p-4 bg-white border rounded shadow-sm">
          <div class="text-sm text-gray-500 mb-2">Demo credentials (for quick trial)</div>
          <div class="grid sm:grid-cols-3 gap-3">
            <div class="p-3 border rounded">
              <div class="font-semibold">Admin</div>
              <div class="text-xs text-gray-600">admin@wow.dukandar.online</div>
              <div class="text-xs text-gray-600">AdminPass123!</div>
            </div>
            <div class="p-3 border rounded">
              <div class="font-semibold">Customer</div>
              <div class="text-xs text-gray-600">test+bot@wow.dukandar.online</div>
              <div class="text-xs text-gray-600">Password123!</div>
            </div>
            <div class="p-3 border rounded">
              <div class="font-semibold">Driver</div>
              <div class="text-xs text-gray-600">driver1@wow.dukandar.online</div>
              <div class="text-xs text-gray-600">DriverPass123!</div>
            </div>
          </div>
        </div>
      </div>

      <div>
        <div class="bg-white border rounded p-4 shadow-sm">
          <div class="text-sm font-medium mb-2">Demo: Recent Bookings</div>
          <ul class="space-y-3">
            <li class="flex items-center justify-between p-3 border rounded">
              <div>
                <div class="font-medium">BK68c5783e287f5</div>
                <div class="text-xs text-gray-500">Status: <span class="font-semibold text-[#F53003]">accepted</span></div>
              </div>
              <div class="text-xs text-gray-500">Driver: Driver 1</div>
            </li>
            <li class="flex items-center justify-between p-3 border rounded">
              <div>
                <div class="font-medium">BK68c5783e2de9f</div>
                <div class="text-xs text-gray-500">Status: pending</div>
              </div>
              <div class="text-xs text-gray-500">Driver: —</div>
            </li>
            <li class="flex items-center justify-between p-3 border rounded">
              <div>
                <div class="font-medium">BK68c5783e2e0cc</div>
                <div class="text-xs text-gray-500">Status: pending</div>
              </div>
              <div class="text-xs text-gray-500">Driver: —</div>
            </li>
          </ul>
        </div>
      </div>
    </section>

    <section id="features" class="py-8">
      <h2 class="text-2xl font-semibold mb-4">Key features</h2>
      <div class="grid sm:grid-cols-3 gap-4">
        <div class="p-4 border rounded">
          <div class="font-semibold">Quick bookings</div>
          <div class="text-sm text-gray-600 mt-1">Create bookings quickly with required defaults.</div>
        </div>
        <div class="p-4 border rounded">
          <div class="font-semibold">Assign drivers</div>
          <div class="text-sm text-gray-600 mt-1">Admin can assign drivers; updates are immediate.</div>
        </div>
        <div class="p-4 border rounded">
          <div class="font-semibold">Soft delete & restore</div>
          <div class="text-sm text-gray-600 mt-1">Recover bookings from trash for safety and audits.</div>
        </div>
      </div>
    </section>

    <section id="how" class="py-8">
      <h2 class="text-2xl font-semibold mb-4">How it works — 3 steps</h2>
      <div class="grid sm:grid-cols-3 gap-6">
        <div class="p-4 border rounded text-center">1. Customer creates booking</div>
        <div class="p-4 border rounded text-center">2. Admin assigns driver</div>
        <div class="p-4 border rounded text-center">3. Driver completes trip</div>
      </div>
    </section>

    <footer class="py-8 text-sm text-gray-600">
      <div class="border-t pt-6">© WowApp — demo server. For production use contact the team.</div>
    </footer>
  </main>

</body>
</html>
