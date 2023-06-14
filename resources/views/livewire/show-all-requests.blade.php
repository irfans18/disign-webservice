<div>
   <div class="pb-12 pt-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
         {{-- <h3 class="mt-6 text-xl">Revocation Requests</h3> --}}
         <div class="flex flex-col mt-6">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
               <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                  <div class="overflow-hidden border-b border-gray-200 rounded-md shadow-md">
                     <table class="min-w-full overflow-x-scroll divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                           <tr>
                              <th scope="col"
                                 class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                 ID
                              </th>
                              <th scope="col"
                                 class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                 Request from
                              </th>
                              <th scope="col"
                                 class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                 certificate id
                              </th>
                              <th scope="col"
                                 class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                 status
                              </th>
                              <th scope="col"
                                 class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                 Aksi
                              </th>
                              {{-- <th scope="col"
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            filepath
                                        </th> --}}
                              {{-- <th scope="col"
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            revocation_detail
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            revoked_at
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            revoked_timestamp
                                        </th> --}}
                           </tr>
                        </thead>
                        @isset($requests)
                           <tbody class="bg-white divide-y divide-gray-200">
                              @foreach ($requests as $row)
                                 <tr class="transition-all hover:bg-gray-100 hover:shadow-lg">
                                    <td class="px-6 py-4 text-sm text-black-500 whitespace-nowrap">
                                       {{ $row->id }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-black-500 whitespace-nowrap">
                                       {{ 'Name : ' . $row->user->name }}
                                       <br>
                                       {{ 'Email : ' . $row->user->email }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-black-500 whitespace-nowrap">
                                       {{ $row->certificate_id }}
                                    </td>
                                    <td
                                       class="px-6 py-4 text-sm whitespace-nowrap 
                                       @if ($row->status == 1) text-blue-500
                                       @elseif ($row->status == 2) red
                                          text-red-500
                                       @else
                                          text-black-500 @endif">
                                       {{ $row->getStatusNameAttribute() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                       <a href=" /requests/{{ $row->id }} "
                                          class="px-2 py-1 bg-blue-400 text-sm rounded-md text-white">Detail</a>
                                    </td>
                                 </tr>

                                 {{-- <tr class="transition-all hover:bg-gray-100 hover:shadow-lg">
                                          <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">2</td>
                                          <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">002</td>
                                          <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">Satria Baja Hitam 2</td>
                                          <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">10</td>
                                          <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">Alfian Prisma Yopiangga 2</td>
                                          <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">Web Petikdua 2</td>
                                          <td class="px-6 py-4 whitespace-nowrap">
                                             <button class="px-2 py-1 bg-blue-400 text-sm rounded-md text-white">Detail</button>
                                          </td>
                                       </tr> --}}
                              @endforeach
                           </tbody>
                        @endisset

                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
