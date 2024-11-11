<h2>Worksite management system</h2>

<h3>Worksite Management Module:</h3>
<ul>
  <li>Worksite entity fields check → it should have not nullable fields</li>
  <li>Worksite routes check → it should have all routes for /worksite</li>

  <li>Create Worksite → As a non-authenticated, I cant create a main worksite</li>
  <li>Create Worksite → As not admin, I cant create a main worksite</li>
  <li>Create Worksite → As an administrator, I want to create a main worksite</li>
  <li>Create Worksite → As an administrator, I want to create a sub worksites</li>
  <li>Create Worksite → As an administrator, should return validation error when no data</li>

  <li>Update Worksite → As a non-authenticated, I cant update a main worksite</li>
  <li>Update Worksite → As not admin, I cant update a main worksite</li>
  <li>Update Worksite → As an administrator, I want to update worksite main info</li>
  <li>Update Worksite → As an administrator, I want to update worksite contractor before worksite finished → This test did not perform any assertions</li>

  <li>List WorkSites → As a non-authenticated, I cant show list of worksites</li>
  <li>List WorkSites → As not admin, I cant show list of worksites</li>
  <li>List WorkSites → As an admin, I can show list of worksites</li>
  <li>List WorkSites → As an admin, I can show list of worksites without customer and category while creating</li>

  <li>Show WorkSites Details → As a non-authenticated, I cant show details of a worksite</li>
  <li>Show WorkSites Details → As not admin, I cant show details of a worksite</li>
  <li>Show WorkSites Details → it should return not found error if worksite not existed in database</li>
  <li>Show WorkSites Details → As an admin, I can show details of a worksite</li>
  <li>Show WorkSites Details → As an admin, I can show details of a worksite with payments and items</li>

  <li>Close WorkSites → As a non-authenticated, I cant close a worksite</li>
  <li>Close WorkSites → As not admin, I cant close a worksite</li>
  <li>Close WorkSites → it should return not found error if worksite not existed in database</li>
  <li>Close WorkSites → it should prevent me closing worksite with active worksites</li>
  <li>Close WorkSites → it should prevent me closing worksite with unpaid payments</li>
  <li>Close WorkSites → As an admin, I can close a worksite with full payments and closed sub worksites</li>

  <li>Assign Contractor to WorkSites → As a non-authenticated, I cant assign contractor to a worksite</li>
  <li>Assign Contractor to WorkSites → As not admin, I cant assign contractor to a worksite</li>
  <li>Assign Contractor to WorkSites → it should return not found error if worksite not existed in database and if contractor not existed</li>
  <li>Assign Contractor to WorkSites → it should add contractor of a worksite</li>
  <li>Assign Contractor to WorkSites → it should update contractor of a worksite</li>
  <li>Assign Contractor to WorkSites → As an admin i can remove contractor of a worksite</li>
</ul>

<h3>Worksite Support Module:</h3>
<ul>
<li>
As an administrator, I should manage categories
of worksite.
</li>
<li>
As an administrator, I should manage customers of worksite.
</li>
<li>
As an administrator, I should manage items and theirs categories of worksite.
</li>
<li>
As an administrator, I should manage workers of worksite.
</li>
</ul>
<h3>Admin Module:</h3>
<ul>
<li>
As an administrator, I should manage categories.
</li>
<li>
As an administrator, I should manage customers.
</li>
<li>
As an administrator, I should manage items and theirs categories.
</li>
<li>
As an administrator, I should manage all payments.
</li>
<li>
As an administrator, I should manage all workers.
</li>
</ul>
