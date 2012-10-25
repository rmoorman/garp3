#   s t a g e s
set :stages, %w(integration staging production)
require 'capistrano/ext/multistage'
set :stage, nil
set :default_stage, "integration"


#   v e r s i o n   c o n t r o l
set :scm, :git


#   r e m o t e   s e r v e r
set :deploy_via, :remote_cache
ssh_options[:forward_agent] = true
set :git_enable_submodules, 1
set :use_sudo, false
set :keep_releases, 3


#   f l o w
after "deploy:update_code", "deploy:cleanup"

namespace :deploy do
    task :update do
    	transaction do
    		update_code
        set_permissions
        spawn
    		symlink
    	end
    end

    task :finalize_update do
    	transaction do
    		run "chmod -R g+w #{releases_path}/#{release_name}"
    	end
    end

    task :symlink do
    	transaction do
    		run "ln -nfs #{current_release} #{deploy_to}/#{current_dir}"
        run "ln -nfs #{deploy_to}/#{current_dir} #{document_root}"
    	end
    end
    
    task :set_permissions do
      transaction do
        run "echo '<?php' > #{current_release}/application/data/cache/pluginLoaderCache.php"
      end
    end

    task :spawn do
      transaction do
        run "php #{current_release}/garp/scripts/garp.php Spawn --e=#{garp_env}"
      end
    end

    task :migrate do
    	# nothing
    end

    task :restart do
    	# nothing
    end
end



#   p r o d u c t i o n   w a r n i n g
task :ask_production_confirmation do
  set(:confirmed) do
    puts <<-WARN

    ========================================================================

      WARNING: You're about to deploy to a live, public server.
      Please confirm that your work is ready for that.

    ========================================================================

    WARN
    answer = Capistrano::CLI.ui.ask "  Are you sure? (Y) "
    if answer == 'Y' then true else false end
  end

  unless fetch(:confirmed)
    puts "\nDeploy cancelled!"
    exit
  end
end

before 'production', :ask_production_confirmation