<?php

namespace Flightsadmin\Flights\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

trait FileHandler
{
    public function spatiePermissionsInstall()
    {
        if ($this->confirm('Do you want to Install Spatie Laravel Permission?', true, true)) {
            $this->permStubDir = __DIR__ . '/../../resources/install/permissions';
            $this->generatePermissionFiles();

            //Updating Routes
            $routeFile = base_path('routes/web.php');
            $routeData = file_get_contents($routeFile);
            $updatedData = $this->filesystem->get($routeFile);
            $spatieRoutes = 
            <<<ROUTES
            Route::view('permissions', 'livewire.permissions.index')->middleware('auth', 'role:super-admin|admin');
            Route::view('roles', 'livewire.roles.index')->middleware('auth', 'role:super-admin|admin');
            Route::view('users', 'livewire.users.index')->middleware('auth', 'role:super-admin|admin|user');
            Route::view('airlines', 'livewire.airlines.index')->middleware('auth', 'role:super-admin|admin|user');
            Route::view('registrations', 'livewire.registrations.index')->middleware('auth', 'role:super-admin|admin|user');
            Route::view('flights', 'livewire.flights.index')->middleware('auth', 'role:super-admin|admin|user');
            Route::view('schedules', 'livewire.schedules.index')->middleware('auth', 'role:super-admin|admin');
            ROUTES;
            $fileHook = "//Route Hooks - Do not delete//";

            if (!Str::contains($updatedData, trim($spatieRoutes))) {
                $UserModelContents = str_replace($fileHook, $fileHook . PHP_EOL . $spatieRoutes, $updatedData);
                $this->filesystem->put($routeFile, $UserModelContents);
                $this->warn($routeFile . ' Updated');
            }

            //Updating NavBar
            $layoutsFile = base_path('resources/views/layouts/app.blade.php');
            $layoutsData = $this->filesystem->get($layoutsFile);
            $spatieNavs  = "\t\t\t\t\t\t
                        <li class=\"nav-item\">\n\t\t\t\t\t\t\t<a href=\"{{ url('/users') }}\" class=\"nav-link\"><i class=\"bi bi-people-fill text-info h5\"></i> Users </a>\n\t\t\t\t\t\t</li>
                        @role('super-admin|admin')
                        <li class=\"nav-item\">\n\t\t\t\t\t\t\t<a href=\"{{ url('/roles') }}\" class=\"nav-link\"><i class=\"bi bi-shield-shaded text-info h5\"></i> Roles </a>\n\t\t\t\t\t\t</li>
                        <li class=\"nav-item\">\n\t\t\t\t\t\t\t<a href=\"{{ url('/permissions') }}\" class=\"nav-link\"><i class=\"bi bi-person-fill-lock text-info h5\"></i> Permissions </a>\n\t\t\t\t\t\t</li>
                        @endrole
                        <li class=\"nav-item\">\n\t\t\t\t\t\t\t<a href=\"{{ url('/airlines') }}\" class=\"nav-link\"><i class=\"bi bi-database-add text-info h5\"></i> Airlines </a>\n\t\t\t\t\t\t</li>
                        <li class=\"nav-item\">\n\t\t\t\t\t\t\t<a href=\"{{ url('/registrations') }}\" class=\"nav-link\"><i class=\"bi bi-clock-history text-info h5\"></i> Registrations </a>\n\t\t\t\t\t\t</li>
                        <li class=\"nav-item\">\n\t\t\t\t\t\t\t<a href=\"{{ url('/flights') }}\" class=\"nav-link\"><i class=\"bi bi-airplane-engines-fill text-info h5\"></i> Flights </a>\n\t\t\t\t\t\t</li>
                        @role('super-admin|admin')
                        <li class=\"nav-item\">\n\t\t\t\t\t\t\t<a href=\"{{ url('/schedules') }}\" class=\"nav-link\"><i class=\"bi bi-newspaper text-info h5\"></i> Schedules </a>\n\t\t\t\t\t\t</li>
                        @endrole";
            $spatieFileHook = "<!--Nav Bar Hooks - Do not delete!!-->";

            if (!Str::contains($layoutsData, $spatieNavs)) {
                $UserModelContents = str_replace($spatieFileHook, $spatieFileHook . PHP_EOL . $spatieNavs, $layoutsData);
                $this->filesystem->put($layoutsFile, $UserModelContents);
                $this->warn($layoutsFile . ' Updated');
            }

            //Updating Kernel
            $kernelFile = app_path('Http/Kernel.php');
            $kernelData = $this->filesystem->get($kernelFile);
            $kerneltemStub = "\t\t//Spatie Permission Traits\n\t\t'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class, \n\t\t'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class, \n\t\t'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,\n\t\t//End Spatie Permission Trait";
            $kernelItemHook = (version_compare(app()->version(), '10.0.0', '>=')) ? 'protected $middlewareAliases = [' : 'protected $routeMiddleware = [';

            if (!Str::contains($kernelData, $kerneltemStub)) {
                $KernelContents = str_replace($kernelItemHook, $kernelItemHook . PHP_EOL . $kerneltemStub, $kernelData);
                $this->filesystem->put($kernelFile, $KernelContents);
                $this->warn('<info>' . $kernelFile . '</info> Updated');
            }

            // Updating User Model
            $userModelFile = app_path('Models/User.php');
            $fileData = $this->filesystem->get($userModelFile);
            $modelReplacements = [
                "class User extends Authenticatable\n{" => "\tuse HasRoles;",
                "namespace App\Models;\n"               => "use Spatie\Permission\Traits\HasRoles;",
                "protected \$fillable = ["              => "\t\t'phone',\n\t\t'photo',\n\t\t'title',",
            ];
            
            foreach ($modelReplacements as $key => $value) {
                if (!Str::contains($fileData, $value)) {
                    $fileData = str_replace($key, $key . PHP_EOL . $value, $fileData);
                    $this->filesystem->put($userModelFile, $fileData);
                    $this->warn($userModelFile . ' Updated with <info>' . trim($value). '</info>');
                }
            }

            $this->line('');
            $this->warn('Publishing Laravel Permissions Files');
            Artisan::call('vendor:publish', ['--provider' => 'Spatie\Permission\PermissionServiceProvider'], $this->getOutput());
            $this->warn('Seeding the Database. Please wait...');
            Artisan::call('migrate:fresh', [], $this->getOutput());
            Artisan::call('optimize:clear', [], $this->getOutput());
            Artisan::call('db:seed', ['--class' => 'AdminDatabaseSeeder'], $this->getOutput());
            Artisan::call('db:seed', ['--class' => 'FlightsDatabaseSeeder'], $this->getOutput());
        }
    }

    public function generatePermissionFiles()
    {
        $files = $this->filesystem->allFiles($this->permStubDir, true);
        foreach ($files as $file) {
            $filePath = $this->replace(Str::replaceLast('.stub', '', $file->getRelativePathname()));
            $fileDir = $this->replace($file->getRelativePath());

            if ($fileDir) {
                $this->filesystem->ensureDirectoryExists($fileDir);
            }
            $this->filesystem->put($filePath, $this->replace($file->getContents()));
            $this->warn('Generated file: <info>' . $filePath . '</info>');
        }
    }
}
