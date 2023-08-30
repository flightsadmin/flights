<?php

namespace Flightsadmin\Flights\Commands;

use Illuminate\Support\Str;
use RecursiveIteratorIterator;
use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class LivewireInstall extends Command
{
    use FileHandler;
	protected $filesystem;
    protected $crudStubDir;
    protected $permStubDir;
    protected $argument;
    private $replaces = [];
	
    protected $signature = 'flights:install';
    protected $description = 'Install Livewire CRUD Generator, compile and publish it\'s assets';

    public function handle()
    {
        $this->filesystem = new Filesystem;
		(new Filesystem)->ensureDirectoryExists(app_path('Livewire'));
		(new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers'));
		(new Filesystem)->ensureDirectoryExists(app_path('Models'));
		(new Filesystem)->ensureDirectoryExists(resource_path('views/livewire'));
		(new Filesystem)->ensureDirectoryExists(resource_path('views/components/layouts'));
		
        if ($this->confirm('This will delete compiled assets in public folder. It will Re-Compile this. Do you want to proceed?', false, true)) { 
            $routeFile = base_path('routes/web.php');
            $routeData = file_get_contents($routeFile);
            if (!str_contains($routeData, '//Route Hooks - Do not delete//')) {
                file_put_contents($routeFile, "\n//Route Hooks - Do not delete//", FILE_APPEND);
            }
            
			if ($this->confirm('Do you want to scaffold Authentication files? Only skip if you have authentication system on your App', false, true)) {
                Artisan::call('ui:auth', ['--force' => true], $this->getOutput());
			}

            $this->line('');
            $deleteFiles = [
                'resources/sass',
                'resources/css',
                'resources/js',
                'public/css',
                'public/js',
                'public/build',
                'public/fonts',
            ];
    
            foreach ($deleteFiles as $deleteFile) {
                if ($this->filesystem->exists($deleteFile)) {
                    $this->filesystem->delete($deleteFile);
                    $this->filesystem->deleteDirectory($deleteFile);
                    $this->warn('Deleted file: <info>' . $deleteFile . '</info>');
                }
            }

            $this->crudStubDir = __DIR__ . '/../../resources/install/crud';
            $this->generateCrudFiles();

            $this->spatiePermissionsInstall();

            // Update Auth Routes
            $authRoutes = "\nAuth::routes(['register' => false]);\nRoute::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');";
            $content = file_get_contents($routeFile);
            $content = str_replace("Auth::routes();\n\nRoute::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');", '', $content);
            if (strpos($content, $authRoutes) === false) {
                $content .= $authRoutes;
            }
            file_put_contents($routeFile, trim($content));            

		$this->line('');
		$this->warn('Running: <info>npm install</info> Please wait...');
		exec('npm install');

        $this->warn('Running: <info>npm run build</info> Please wait...');
        $this->line('');
		exec('npm run build');

        $this->info('Installation Complete, few seconds please, let us optimize your site');
        $this->warn('Removing Dumped node_modules files. Please wait...');
		
		tap(new Filesystem, function ($npm) {
            $npm->deleteDirectory(base_path('node_modules'));
            $npm->deleteDirectory(base_path('resources/views/layouts'));
            $npm->delete(base_path('yarn.lock'));
            $npm->delete(base_path('webpack.mix.js'));
            $npm->delete(base_path('package-lock.json'));
        });
        $this->line('');

        $viewsDirectory = resource_path('views'); // Adjust this path if needed
        $searchExtends = "@extends('layouts.app')";
        $replaceExtends = "@extends('components.layouts.app')";
        $this->correctLayoutExtention($viewsDirectory, $searchExtends, $replaceExtends);
        $this->line('');
        
        $this->warn('All set, Your Flights are ready to take off');		
	  }
		else $this->warn('Installation Aborted, No file was changed');
    }
	
	public function generateCrudFiles()
    {
        $files = $this->filesystem->allFiles($this->crudStubDir, true);
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
    
    public function correctLayoutExtention($directory, $searchExtends, $replaceExtends) {
        $dir = new RecursiveDirectoryIterator($directory);
        $iterator = new RecursiveIteratorIterator($dir);
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $filePath = $file->getPathname();
                $content = file_get_contents($filePath);
                
                $newContent = str_replace($searchExtends, $replaceExtends, $content);
                
                if ($newContent !== $content) {
                    file_put_contents($filePath, $newContent);
                    $this->line("Replaced $searchExtends in: $filePath with $replaceExtends");
                }
            }
        }
    }

    private function replace($content)
    {
        foreach ($this->replaces as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }
        return $content;
    }
}