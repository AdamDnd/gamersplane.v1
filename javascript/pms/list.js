controllers.controller('pmList', ['$scope', '$http', 'CurrentUser', 'DeletePM', function ($scope, $http, CurrentUser, DeletePM) {
	pathElements = getPathElements();
	$scope.pagination = { numItems: 0, itemsPerPage: PAGINATE_PER_PAGE };
	CurrentUser.load().then(function () {
		$scope.CurrentUser = CurrentUser.get();
		if (!$scope.CurrentUser) 
			window.location.href = '/';

		if ($.urlParam('page')) 
			$scope.pagination.current = parseInt($.urlParam('page'));
		else 
			$scope.pagination.current = 1;
		$scope.box = pathElements[1] == 'outbox'?'Outbox':'Inbox';

		$scope.spinnerPause = true;
		$scope.getPMs = function () {
			$scope.spinnerPause = false;
			$scope.$emit('pageLoading');
			$http.post(API_HOST + '/pms/get/', { box: $scope.box, page: $scope.pagination.current }).success(function (data) {
				if (data.success) {
					data.pms.forEach(function (value, key) {
						data.pms[key].datestamp = convertTZ(value.datestamp, 'YYYY-MM-DD HH:mm:ss', 'MMMM D, YYYY h:mm a');
					});
					$scope.pms = data.pms;
					$scope.pagination.numItems = data.totalCount;
					$scope.$emit('pageLoading');
					$scope.spinnerPause = true;
				}
			});
		};
		$scope.getPMs();

		$scope.switchBox = function ($event, box) {
			$event.preventDefault();
			newBox = box.capitalizeFirstLetter();
			if ($scope.box != newBox) {
				$scope.box = newBox;
				$scope.getPMs();
			}
		};

		$scope.delete = function (pmID) {
			DeletePM(pmID).success(function (data) {
				if (!isUndefined(data.deleted)) 
					$scope.getPMs();
			});
		};
	});
}]);