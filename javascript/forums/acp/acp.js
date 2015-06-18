$(function () {
	$('a.sprite.cross, a.newPermission, a.permission_delete').colorbox();
});

controllers.controller('forums_acp', function ($scope, $http, $sce, $filter, $timeout, currentUser) {
	currentUser.then(function (currentUser) {
		pathElements = getPathElements();
		$scope.forumID = pathElements[2];
		$scope.currentSection = 'details';
		$scope.list = {};
		$scope.details = {};
		$scope.permissions = {};
		$scope.permissionTypes = [
			{ 'key': 'read', 'label': 'Read' },
			{ 'key': 'write', 'label': 'Write' },
			{ 'key': 'editPost', 'label': 'Edit Post' },
			{ 'key': 'deletePost', 'label': 'Delete Post' },
			{ 'key': 'createThread', 'label': 'Create Thread' },
			{ 'key': 'deleteThread', 'label': 'Delete Thread' },
			{ 'key': 'addRolls', 'label': 'Add Rolls' },
			{ 'key': 'addDraws', 'label': 'Add Draws' },
			{ 'key': 'moderate', 'label': 'Moderate' }
		];
		if (pathElements[3] != null && ['details', 'subforums', 'permissions'].indexOf(pathElements[3]) != -1) 
			$scope.currentSection = pathElements[3];
		else if (pathElements[3] == 'groups') 
			$scope.currentSection = null;
		$scope.setSection = function (section) {
			if (['details', 'subforums', 'permissions'].indexOf(section) != -1) 
				$scope.currentSection = section;
			else if ($scope.details.isGameForum && section == 'groups') 
				$scope.currentSection = 'groups';
		};
		function getForumDetails(forumID) {
			$http.post(API_HOST + '/forums/acp/details/', { forumID: forumID }).success(function (data) {
				if (data.failed) {
					document.location = '/forums/';
				} else {
					if ($scope.currentSection == null && data.details.isGameForum) 
						$scope.currentSection = 'groups';
					if ($scope.forumID != forumID)
						$scope.currentSection = 'details';
					$scope.forumID = forumID;
					$scope.list = data.list;
					$scope.details = data.details;
					$scope.permissions = data.permissions;

					$scope.combobox.groups = []
					for (key in $scope.details.gameDetails.groups) 
						if (!$scope.details.gameDetails.groups[key].permissionSet)
							$scope.combobox.groups.push({ 'id': $scope.details.gameDetails.groups[key].groupID, 'value': $scope.details.gameDetails.groups[key].name });
				}
			});
		}
		$scope.changeForum = function (forumID, isAdmin) {
			if (!isAdmin) 
				return;
			getForumDetails(forumID);		
		}
		getForumDetails($scope.forumID);

		$scope.saveDetails = function () {
			console.log($scope.details);
		};

		$scope.combobox = {};
		$scope.combobox.search = { 'groups': '' };
		$scope.cb_groups = '';
		$scope.renderedDirectives = { 'groups': false };
		$scope.newGroupPermission = { 'groupID': null };
		$scope.newGroup = { 'name': '' };
		$scope.editingGroup = null;
		$scope.confirmGroupDelete = null;
		groupNameHold = '';
		$scope.createGroup = function () {
			if ($scope.newGroup.name.length < 3) 
				return;

			$http.post(API_HOST + '/forums/acp/createGroup/', { 'forumID': $scope.forumID, 'name': $scope.newGroup.name }).success(function (data) {
				if (data.success) {
					$scope.details.gameDetails.groups.push({ 'groupID': data.groupID, 'name': $scope.newGroup.name, 'permissionSet': false });
					$scope.newGroup.name = '';
				}
			});
		};
		$scope.editGroup = function (groupID, key) {
			$scope.editingGroup = groupID;
			groupNameHold = $scope.details.gameDetails.groups[key].name;
		};
		$scope.cancelEditing = function () {
			$scope.details.gameDetails.groups[key].name = groupNameHold;
			$scope.editingGroup = null;
		};
		$scope.saveGroup = function (groupID, key) {
			$http.post(API_HOST + '/forums/acp/editGroup/', { 'groupID': groupID, 'name': $scope.details.gameDetails.groups[key].name }).success(function (data) {
				if (data.success) {
					$scope.details.gameDetails.groups[key].name = data.name;
					groupNameHold = '';
					$scope.editingGroup = null;
				} else 
					$scope.details.gameDetails.groups[key].name = groupNameHold;
			});
		};
		$scope.deleteGroup = function (groupID) {
			$scope.confirmGroupDelete = groupID;
		};
		$scope.cancelDelete = function () {
			$scope.confirmGroupDelete = null;
		};
		$scope.confirmDelete = function (groupID, key) {
			$http.post(API_HOST + '/forums/acp/deleteGroup/', { 'groupID': groupID }).success(function (data) {
				if (data.success) 
					delete $scope.details.gameDetails.groups[key]
				$scope.confirmGroupDelete = null;
			});
		}

		$scope.editingPermission = null;
		$scope.togglePermissionsEdit = function (permission) {
			if (permission.type == 'general') 
				curKey = 'general';
			else 
				curKey = permission.type + '_' + permission.id;
			if ($scope.editingPermission == null || $scope.editingPermission != curKey) 
				$scope.editingPermission = curKey;
			else 
				$scope.editingPermission = null;
		};
		$scope.changePermission = function (ref, pType, value) {
			type = ref.indexOf('_') >= 0?ref.split('_')[0]:'general';
			if (type == 'general') 
				$scope.permissions.general[pType] = value;
			for (key in $scope.permissions[type])
				if ($scope.permissions[type][key].ref == ref)
					$scope.permissions[type][key][pType] = value;
		};
		$scope.savePermission = function (permission) {
			$http.post(API_HOST + '/forums/acp/savePermission/', { forumID: $scope.forumID, permission: permission }).success(function (data) {
				if (data.success) 
					$scope.editingPermission = null;
			});
		};
		$scope.addGroupPermission = function () {
			console.log($scope.newGroupPermission);
		}
	});
});