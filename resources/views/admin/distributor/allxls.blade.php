<meta charset="UTF-8">

<div class="dataTables_wrapper form-inline dt-bootstrap">
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-hover dataTable" role="grid">
                <thead>
                <tr role="row">
                    <th>ID</th>
                    <th>名称</th>
                    <th>地区</th>
                    <th>地址</th>
                    <th>创建时间</th>
                    <th>状态</th>
                    <th>联系人</th>
                    <th>联系电话</th>
                </thead>
                <tbody>
                @foreach($distributor as $distributor)
                    <tr role="row">
                        <td>{{ $distributor->id }}</td>
                        <td>{{ $distributor->name }}</td>
                        <td>{{ $distributor->area["name"]}}</td>
                        <td>{{ $distributor->area["display"]}}</td>
                        <td>{{ $distributor->created_at }}</td>
                        <td>{{ $distributor->deletedDisplay }}</td>
                        <th>{{ $distributor->contact }}</th>
                        <th>{{ $distributor->telephone }}</th>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
