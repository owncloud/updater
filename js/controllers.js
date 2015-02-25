/* global oc_requesttoken */

function updateCtrl($scope, $http) {
	$scope.step = 0;
	$scope.backup = '';
	$scope.version = '';
	$scope.url = '';

	$scope.fail = function (data) {
		var message = t('updater', '<strong>The update was unsuccessful.</strong><br />Please check logs at admin page and report this issue to the <a href="https://github.com/owncloud/apps/issues" target="_blank">ownCloud community</a>.');
		if (data && data.message) {
			message = data.message;
		}
		$('<div></div>').hide().append($('<p></p>').addClass('updater-warning-p').append(message)).addClass('warning').appendTo($('.updater-progress')).fadeIn();
	};

	$scope.crash = function () {
		var message = t('updater', '<strong>Server error.</strong> Please check web server log file for details');
		$('<div></div>').hide().append($('<p></p>').addClass('updater-warning-p').append(message)).addClass('warning').appendTo($('.updater-progress')).fadeIn();
	};

	$scope.update = function () {
		if ($scope.step === 0) {
			$('.updater-progress').empty().show();
			$('.upd-step-title').show();
			$('.track-progress li').first().addClass('current');
			$('.updater-spinner').hide();
			$('.updater-spinner:eq(0)').fadeIn();
			$('#updater-start').hide();
			$('#update-info').hide();

			$http.get(OC.filePath('updater', 'ajax', 'backup.php'), {headers: {'requesttoken': oc_requesttoken}})
				.success(function (data) {
					if (data && data.status && data.status === 'success') {
						$scope.step = 1;
						$scope.backup = data.backup;
						$scope.version = data.version;
						$scope.url = data.url;
						$scope.update();
					} else {
						$scope.fail(data);
						$('#updater-start').text(t('updater', 'Retry')).fadeIn();
					}
				})
				.error($scope.crash);

		} else if ($scope.step === 1) {
			$('.track-progress li.current').addClass('done');
			$('.updater-spinner').fadeOut();
			$('.updater-spinner:eq(1)').fadeIn();
			$('.track-progress li.current').next().addClass('current');
			$('.track-progress li.done').removeClass('current');
			$('<p></p>').hide().append(t('updater', 'Here is your backup:') + ' ' + $scope.backup).appendTo($('.updater-progress')).fadeIn();
			
			$http.post(
				OC.filePath('updater', 'ajax', 'download.php'), {
					url: $scope.url,
					version: $scope.version
				},
				{headers: {'requesttoken': oc_requesttoken}}
			).success(function (data) {
					if (data && data.status && data.status === 'success') {
						$scope.step = 2;
						$scope.update();
					} else {
						$scope.fail(data);
					}
				})
				.error($scope.crash);

		} else if ($scope.step === 2) {
			$('.track-progress li.current').addClass('done');
			$('.updater-spinner').fadeOut();
			$('.updater-spinner:eq(2)').fadeIn();
			$('.track-progress li.current').next().addClass('current');
			$('.track-progress li.done').removeClass('current');
			
			$http.post(
				OC.filePath('updater', 'ajax', 'update.php'),
				{
					url: $scope.url,
					version: $scope.version,
					backupPath: $scope.backup
				},
				{headers: {'requesttoken': oc_requesttoken}}
			).success(function (data) {
					if (data && data.status && data.status === 'success') {
						$scope.step = 3;
						$('.track-progress li.current').addClass('done');
						$('.track-progress li.done').removeClass('current');
						$('.updater-spinner').fadeOut();
						var href = '/',
							title = t('updater', 'Proceed');
						if (OC.webroot !== '') {
							href = OC.webroot;
						}
						$('<p></p>').hide().addClass('updater-space-bottom').append(t('updater', '<strong>All done.</strong> Click to the link below to start database upgrade.')).appendTo($('.updater-progress')).fadeIn();
						$('<p></p>').hide().addClass('bold').append($('<a href="' + href + '">' + title + '</a>').addClass('button')).appendTo($('.updater-progress')).fadeIn();
					} else {
						$scope.fail(data);
					}
				})
				.error($scope.crash);
		}
	};
}

function backupCtrl($scope, $http) {

	$scope.list = function() {
		$http.get(OC.filePath('updater', 'ajax', 'backup/list.php'), {headers: {'requesttoken': oc_requesttoken}})
		.success(function (data) {
			$scope.entries = data.data;
		});
	};	

	$scope.list();

	$scope.doDelete = function (name) {
		$http.get(OC.filePath('updater', 'ajax', 'backup/delete.php'), {
			headers: {'requesttoken': oc_requesttoken},
			params: {'filename': name}
		}).success(function () {
			$http.get(OC.filePath('updater', 'ajax', 'backup/list.php'), {headers: {'requesttoken': oc_requesttoken}})
				.success(function (data) {
					$scope.entries = data.data;
				});
		});
	};

	$scope.doDownload = function (name) {
		window.open(OC.filePath('updater', 'ajax', 'backup/download.php') +
			'?requesttoken=' + oc_requesttoken +
			'&filename=' + name
		);
	};

	$scope.backup = function () {
		$('.doing-backup').fadeIn();
		$('#backup-start').fadeOut();
		// Remove all errors if user retries
		$('.backup-warning-p').parent().remove();

		$http.get(OC.filePath('updater', 'ajax', 'manual_backup.php'), {headers: {'requesttoken': oc_requesttoken}})
			.success(function (data) {
				if (data && data.status && data.status === 'success') {

					$('.doing-backup').fadeOut();

					$('<p></p>')
						.hide()
						.addClass('updater-space-bottom')
						.append(t('updater', '<strong>Backup done.</strong>'))
						.appendTo($('.backup-progress'))
						.fadeIn();

					$scope.list();

					$('#backup-start').fadeIn();
					
				} else {
					$('.doing-backup').fadeOut();

					$scope.fail(data);
					$('#backup-start').text(t('updater', 'Retry')).fadeIn();
				}
			})
			.error($scope.crash);
	};

	$scope.fail = function (data) {
		var message = t('updater', '<strong>The backup was unsuccessful.</strong><br />Please check logs at admin page and report this issue to the <a href="https://github.com/owncloud/apps/issues" target="_blank">ownCloud community</a>.');
		if (data && data.message) {
			message = data.message;
		}
		$('<div></div>').hide().append($('<p></p>').addClass('backup-warning-p').append(message)).addClass('warning').appendTo($('.backup-progress')).fadeIn();
	};

	$scope.crash = function () {
		var message = t('updater', '<strong>Server error.</strong> Please check web server log file for details');
		$('<div></div>').hide().append($('<p></p>').addClass('backup-warning-p').append(message)).addClass('warning').appendTo($('.backup-progress')).fadeIn();
	};
}
