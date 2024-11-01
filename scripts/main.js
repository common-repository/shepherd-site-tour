document.addEventListener("DOMContentLoaded", function() {

  var deactivate = document.querySelectorAll(".active[id*='eager'] .deactivate a");

  // We only need to alert the user if only 1 eager plugin is active
  if (deactivate.length === 1) {

    // add a "generic" event listener in case the user clicks deactivate before the ajax call completes (with install data)
    deactivate[0].addEventListener('click', genericListener);

    function genericListener(e) {
      var confirmed = confirm("By deactivating this Eager plugin, you may be deactivating other Eager apps on this site. Are you sure you want to do this?");
      if (!confirmed) {
        e.preventDefault();
        return;
      }
    }

    // hit eager_get_installs endpoint for install data

    var request = new XMLHttpRequest();
    var body = 'action=eager_get_installs';

    request.addEventListener('load', handleDeactivation);
    request.open('POST', ajaxurl, true);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(body);

    function handleDeactivation() {
      var res = JSON.parse(this.responseText);
      var installs = res.installs;
      if (installs.length) {
        var installedApps = '';
        for (var i = 0; i < installs.length; i++) {
          installedApps += installs[i].app.title + '\n';
        }
        deactivate[0].removeEventListener('click', genericListener);
        deactivate[0].addEventListener("click", function(e) {
          var confirmed = confirm("By deactivating this Eager plugin, you'll also be deactivating the following Eager apps: \n\n" + installedApps + "\nAre you sure you want to do this?");
          if (!confirmed) {
            e.preventDefault();
            return;
          }
        });
      }
    }
  }

});